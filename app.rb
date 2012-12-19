# encoding=utf-8

require 'sinatra'
require 'sinatra/reloader' if development?
require 'sinatra/session'
require 'mysql'
require './lib/yutarbbs'
also_reload './lib/yutarbbs.rb' if development?

EMOTICON_DIR = File.expand_path 'views'

Encoding.default_external = Encoding::UTF_8

set :layout, true
set :session_name, 'yutarbbs'
set :session_secret, ''

helpers Yutarbbs

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
      if update :users, 'uid = ?', params[:uid], :passwd => passwd
        alert '변경되었습니다.'
      else
        error 400, alert('변경 실패!')
      end
    else
      error 400, alert('암호 이상해!')
    end
  elsif params[:phone]
    puts now
    update :users, 'uid = ?', params[:uid], params.merge(:updated_on => now)
    redirect back
  end
end

get '/users' do
  session!
  @users = fetch_all 'SELECT year, name, email, phone, remark FROM users ORDER BY year DESC, name'
  erb :users
end

get '/forum/*' do |fid|
  call env.merge 'PATH_INFO' => "/forum/#{fid}/1"
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

get '/thread/*' do |tid|
  session!
  # if ($_COOKIE['lasttid'] != $tid) {
  #   update('threads', 'hits = hits + 1', array('tid = ? AND uid != ?', $tid, $my->uid), 1);
  #   setcookie('lasttid', $tid);
  # }
  @thread = fetch_one 'SELECT tid, fid, subject, t.uid, year, name, phone, email, remark, message, UNIX_TIMESTAMP(created_at) created, attachment FROM threads t INNER JOIN users USING (uid) WHERE tid = ? LIMIT 1', tid
  not_found unless @thread
  @messages = fetch_all 'SELECT mid, message, uid, year, name, UNIX_TIMESTAMP(created_at) created FROM messages INNER JOIN users USING (uid) WHERE tid = ? ORDER BY created_at', tid
  @path = "./www/attachment/#{@thread[:tid]}-#{@thread[:attachment]}"
  @size = nil
  # if (is_readable($path) && is_file($path))
    # $size = filesize($path);
  erb :thread
end

get '/thread/*/*' do |tid, modifier|
  session!
  case modifier
  when 'next'
    thread = fetch_one 'SELECT b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid<a.tid ORDER BY b.tid DESC LIMIT 1', tid
  when 'prev'
    thread = fetch_one 'SELECT b.tid FROM threads a, threads b WHERE a.tid=? AND b.fid=a.fid AND b.tid>a.tid ORDER BY b.tid LIMIT 1', tid
  end
  redirect to "/thread/#{thread[:tid]}" if thread
  error 204
end

get '/rss' do
  'ok'
end

get '/edit_thread' do
  session!
  'ok'
end

get '/delete_thread' do
  session!
  'ok'
end

get '/delete_message' do
  session!
  'ok'
end

get '/message' do
  session!
  'ok'
end

get '/emoticons' do
  session!
  @emoticons = Dir[File.join(EMOTICON_DIR, '*')].map { |e| File.basename(e).sub /\.[^.]*$/, '' }
  erb :emoticons
end

post '/emoticons' do
  sesseion!
  if emoticon = params[:emoticon] and emoticon[:tempfile]
    FileUtils.cp emoticon[:tempfile], File.join(EMOTICON_DIR, File.basename(emoticon[:filename]))
  end
end

get '/emo/*' do |name|
  # send_file File.join(EMOTICON_DIR, name)
  not_found
end
