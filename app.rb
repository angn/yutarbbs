# encoding=utf-8

require 'sinatra'
require 'sinatra/reloader' if development?
require 'sinatra/session'
require "sinatra/cookies"
require 'yutarbbs'
require 'builder'
also_reload 'lib/yutarbbs' if development?
also_reload 'lib/yutarbbs/text' if development?
also_reload 'lib/yutarbbs/database' if development?

raise "ATTACHMENT_DIR is not set." unless ENV['ATTACHMENT_DIR']
ATTACHMENT_DIR = File.expand_path ENV['ATTACHMENT_DIR']
raise "#{ATTACHMENT_DIR} doesn't exist." unless File.directory? ATTACHMENT_DIR

raise "EMOTICON_DIR is not set." unless ENV['EMOTICON_DIR']
EMOTICON_DIR = File.expand_path ENV['EMOTICON_DIR']
raise "#{EMOTICON_DIR} doesn't exist." unless File.directory? EMOTICON_DIR

Encoding.default_external = Encoding::UTF_8

set :layout, true
set :session_name, 'yutarbbs'
set :session_secret, "#{__FILE__}#{ATTACHMENT_DIR}#{EMOTICON_DIR}"

helpers Yutarbbs, Yutarbbs::Text
include Yutarbbs::Database

get '/' do
  @notice = fetch_one 'SELECT * FROM threads WHERE fid = 1 ORDER BY created_at DESC'
  redirect to "/thread/#{@notice[:tid]}" if session?
  erb :index
end

get '/gateway' do
  session_end!
  redirect back
end

post '/gateway' do
  user = fetch_one 'SELECT uid, year, name, userid, IFNULL(updated_on + INTERVAL 3 MONTH, 0) < NOW() outdated FROM users WHERE userid = ? AND passwd = ? LIMIT 1', params[:userid], hashpasswd(params[:passwd])
  if user
    session_start!
    user.each do |k,v|
      session[k] = v unless k == :outdated
    end
    # session[:persistent] = params[:persistent]
    puts user
    redirect to '/me' if user[:outdated].nonzero?
  else
    session_end!
  end
  redirect back
end

get '/login' do
  erb :auth
end

get '/me' do
  session!
  @user = fetch_one 'SELECT uid, userid, year, name, email, phone, remark, UNIX_TIMESTAMP(updated_on) updated FROM users WHERE uid = ?', session[:uid]
  not_found unless @user
  erb :me
end

post '/me' do
  session!
  if params[:passwd]
    if params[:passwd] == params[:passwd2]
      passwd = hashpasswd params[:passwd]
      if update :users, 'uid = ?', session[:uid], :passwd => passwd
        alert '변경되었습니다.'
      else
        error 400, alert('변경 실패!')
      end
    else
      error 400, alert('암호 이상해!')
    end
  elsif params[:phone]
    puts now
    update :users, 'uid = ?', session[:uid],
      :email => params[:email],
      :phone => params[:phone],
      :remark => params[:remark],
      :updated_on => now
    redirect back
  end
end

get '/users' do
  session!
  @users = fetch_all 'SELECT year, name, email, phone, remark FROM users ORDER BY year DESC, name'
  erb :users
end

get '/forum/*/*' do |fid, page|
  session!
  @fid = fid.to_i
  not_found unless forum_name[@fid]
  @page = [ 1, page.to_i ].max
  @max_page = fetch_one('SELECT GREATEST(1, CEILING(COUNT(*) / 15)) n FROM threads WHERE fid = ?', fid)[:n].to_i
  if @keyword = params[:keyword]
    @threads = fetch_all 'SELECT tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? AND (UPPER(name) = UPPER(?) OR INSTR(UPPER(subject), UPPER(?)) OR INSTR(UPPER(message), UPPER(?))) ORDER BY created_at DESC', fid, @keyword, @keyword, @keyword
  else
    @threads = fetch_all "SELECT tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, hits, attachment FROM threads INNER JOIN users USING (uid) WHERE fid = ? ORDER BY created_at DESC LIMIT #{@page * 15 - 15}, 15", fid
  end

  if @threads.length.nonzero?
    tids = @threads.map { |e| e[:tid] } * ','
    rs = fetch_all "SELECT tid, COUNT(tid) replies, MAX(created_at) + INTERVAL 1 DAY > NOW() updated FROM messages WHERE tid IN (#{tids}) GROUP BY tid"
    @threads.each do |e|
      e.update rs.find { |r| r[:tid] == e[:tid] } || {}
    end
  end

  erb :forum
end

get '/forum/*' do |fid|
  call env.merge 'PATH_INFO' => "/forum/#{fid}/1"
end

get '/thread/*/*' do |tid, modifier|
  case modifier
  when 'next'
    thread = fetch_one 'SELECT b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid<a.tid ORDER BY b.tid DESC LIMIT 1', tid
  when 'prev'
    thread = fetch_one 'SELECT b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid>a.tid ORDER BY b.tid LIMIT 1', tid
  end
  redirect to "/thread/#{thread[:tid]}" if thread
  error 204
end

get '/thread/*' do |tid|
  session!
  if cookies[:lasttid] != tid
    cookies[:lasttid] = tid
    update :threads, 'tid=? AND uid!=?', tid, session[:uid], 'hits=hits+1'
  end
  @thread = fetch_one 'SELECT tid, fid, subject, t.uid, year, name, phone, email, remark, message, UNIX_TIMESTAMP(created_at) created, NULLIF(attachment, "") attachment FROM threads t INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', tid
  not_found unless @thread
  @messages = fetch_all 'SELECT mid, message, uid, year, name, UNIX_TIMESTAMP(created_at) created FROM messages INNER JOIN users USING (uid) WHERE tid = ? ORDER BY created_at', tid
  @path = "#{ATTACHMENT_DIR}/#{@thread[:tid]}-#{@thread[:attachment]}"
  @size = File.readable?(@path) && File.size(@path)
  erb :thread
end

get '/attachment/*/*' do |tid, filename|
  session!
  @path = "#{ATTACHMENT_DIR}/#{tid}-#{filename}"
  not_found unless File.readable? @path
  send_file @path
end

get '/rss' do
  @threads = fetch_all 'SELECT fid, tid, subject, year, name, UNIX_TIMESTAMP(created_at) created, message FROM threads INNER JOIN users USING (uid) ORDER BY created_at DESC LIMIT 20'
  content_type 'application/rss+xml'
  Builder::XmlMarkup.new.rss :version => '2.0' do |xml|
    xml.channel do
      xml.title 'yutar.net'
      xml.link to '/'
      xml.description 'yutar. the premium.'
      @threads.each do |thread|
        xml.item do
          xml.title "[#{forum_name[thread[:fid]]}] #{thread[:subject]}"
          xml.link to "/thread/#{thread[:tid]}"
          xml.description formattext(thread[:message]).gsub(/\n/, '<br/>')
          xml.author "#{'%02d' % thread[:year]}#{thread[:name]}"
          xml.pubDate Time.at(thread[:created]).strftime('%a, %-d %b %Y %T %z')
          xml.category forum_name[thread[:fid]]
        end
      end
    end
  end
end

get '/edit_thread/forum/*' do |fid|
  session!
  @thread = { :fid => fid.to_i, :tid => 0 }
  erb :edit_thread
end

get '/edit_thread/*' do |tid|
  session!
  @thread = fetch_one 'SELECT tid, fid, subject, message, UNIX_TIMESTAMP(created_at) created FROM threads INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', tid
  not_found unless @thread
  erb :edit_thread
end

post '/edit_thread/forum/*' do |fid|
  session!
  error 204 if params[:subject] !~ /\S/
  attachment = params[:attachment]
  tid = insert :threads,
    :subject => params[:subject],
    :message => params[:message],
    :fid => fid,
    :created_at => now,
    :uid => session[:uid],
    :attachment => attachment && attachment[:filename] || ''
  error unless tid
  if attachment && attachment[:filename]
    store attachment[:tempfile],
      "#{ATTACHMENT_DIR}/#{tid}-#{attachment[:filename]}"
  end
  redirect to "/thread/#{tid}"
end

post '/edit_thread/*' do |tid|
  session!
  error 204 if params[:subject] !~ /\S/
  attachment = params[:attachment]
  update :threads, 'tid = ? AND uid = ?', tid, session[:uid],
    :subject => params[:subject],
    :message => params[:message],
    :attachment => attachment && attachment[:filename] || ''
  if attachment && attachment[:filename]
    store attachment[:tempfile],
      "#{ATTACHMENT_DIR}/#{tid}-#{attachment[:filename]}"
  end
  redirect to "/thread/#{tid}"
end

get '/delete_thread/*' do |tid|
  session!
  thread = fetch_one 'SELECT fid, tid, attachment FROM threads WHERE tid = ? LIMIT 1', tid
  not_found unless thread
  delete :threads, 'tid = ? AND uid = ?', tid, session[:uid]
  FileUtils.rm_f "#{ATTACHMENT_DIR}/#{tid}-#{thread[:attachment]}"
  redirect to "/forum/#{thread[:fid]}"
end

get '/delete_message/*' do |mid|
  session!
  delete :messages, 'mid = ? AND uid = ?', mid, session[:uid] if params[:y] == '1'
  redirect back
end

post '/message' do
  session!
  not_found unless params[:tid]
  insert :messages,
    :tid => params[:tid],
    :message => params[:message],
    :created_at => now,
    :uid => session[:uid]
  redirect back
end

get '/emoticons' do
  session!
  @emoticons = Dir[File.join(EMOTICON_DIR, '*')].map { |e| File.basename(e).sub /\.[^.]*$/, '' }
  erb :emoticons
end

post '/emoticons' do
  session!
  emoticon = params[:emoticon]
  error 204 unless emoticon and emoticon[:tempfile]
  error 400, alert('100KB 이하로 해줘요.') if 100 * 1024 < File.size(emoticon[:tempfile])
  store emoticon[:tempfile], "#{EMOTICON_DIR}/#{emoticon[:filename]}"
  redirect back
end

IMAGE_EXT = %w/jpg jpeg gif png JPG JPEG GIF PNG/

get '/emo/*' do |name|
  candidates = IMAGE_EXT.map { |ext| "#{EMOTICON_DIR}/#{name}.#{ext}" }
  path = candidates.find { |path| File.readable? path }
  not_found unless path
  send_file path
end
