# encoding=utf-8

require 'sinatra'
require 'sinatra/reloader' if development?
require 'sinatra/session'
require "sinatra/cookies"
require 'yutarbbs'
require 'builder'
also_reload 'lib/yutarbbs' if development?
also_reload 'lib/yutarbbs/text' if development?
also_reload 'lib/yutarbbs/model' if development?

raise "ATTACHMENT_DIR is not set." unless ENV['ATTACHMENT_DIR']
ATTACHMENT_DIR = File.expand_path ENV['ATTACHMENT_DIR']
raise "#{ATTACHMENT_DIR} doesn't exist." unless File.directory? ATTACHMENT_DIR

raise "EMOTICON_DIR is not set." unless ENV['EMOTICON_DIR']
EMOTICON_DIR = File.expand_path ENV['EMOTICON_DIR']
raise "#{EMOTICON_DIR} doesn't exist." unless File.directory? EMOTICON_DIR

Encoding.default_external = Encoding::UTF_8

disable :dump_errors
enable :layout
set :session_name, 'yutarbbs'
set :session_secret, "#{__FILE__}#{ATTACHMENT_DIR}#{EMOTICON_DIR}"

helpers Yutarbbs, Yutarbbs::Text
include Yutarbbs::Model

before do
  env['rack.session.options'][:expire_after] =
    cookies['keeplogin'] ? 60 * 60 * 24 * 14 : nil
end

get '/' do
  @notice = Article.last fid: 1
  redirect to "/thread/#{@notice.id}" if session?
  erb :index
end

get '/gateway' do
  session_end!
  redirect to '/'
end

post '/gateway' do
  user = User.first(
    userid: params[:userid],
    passwd: hashpasswd(params[:passwd]))
  outdated = Time.now - user.updated_on.to_time > 90 * 24 * 60 * 60 # 3 months
  if user
    session_start!
    %w/id year name userid/.each do |k|
      session[k.to_sym] = user[k]
    end
    response.set_cookie :keeplogin, value: '1',
      expires: params[:keeplogin] ? Time.now + 365 * 86400 : Time.at(0)
    redirect to '/me' if outdated
  else
    session_end!
  end
  redirect back
end

get '/login' do
  redirect to '/' if session?
  erb :auth
end

get '/me' do
  session!
  @user = User.get(session[:id]) or not_found
  erb :me
end

post '/me' do
  session!
  if params[:passwd]
    if params[:passwd] == params[:passwd2]
      if User.get(session[:id]).update passwd: hashpasswd(params[:passwd])
        alert '변경되었습니다.'
      else
        error 400, alert('변경 실패!')
      end
    else
      error 400, alert('암호 이상해!')
    end
  elsif params[:phone]
    User.get(session[:id]).update(
      email: params[:email],
      phone: params[:phone],
      remark: params[:remark],
      updated_on: Time.now)
    redirect back
  end
end

get '/users' do
  session!
  @users = User.all :order => [ :year.desc, :name ]
  erb :users
end

get '/forum/*/*' do |fid, page|
  session!
  @fid = fid.to_i
  not_found unless forum_name[@fid]
  @page = [ 1, page.to_i ].max
  @max_page = [ 1, (Article.count(fid: fid) + 14) / 15 ].max
  if @keyword = params[:q]
    @threads = User.all(name: @keyword).articles(fid: fid)
    articles = Article.all fid: fid
    @threads += articles.all(:subject.like => "%#{@keyword}%")
    @threads += articles.all(:message.like => "%#{@keyword}%")
  else
    @threads = Article.all fid: fid, order: [ :id.desc ],
      offset: @page * 15 - 15, limit: 15
  end

  erb :forum
end

get '/forum/*' do |fid|
  call env.merge 'PATH_INFO' => "/forum/#{fid}/1"
end

get '/thread/*/*' do |id, modifier|
  case modifier
  when 'next'
    article = Article.first fid: Article.get(id).fid, :id.lt => id, order: [ :id.desc ]
  when 'prev'
    article = Article.first fid: Article.get(id).fid, :id.gt => id, order: [ :id.asc ]
  end
  redirect to "/thread/#{article.id}" if article
  error 204
end

get '/thread/*' do |tid|
  session!
  if cookies[:lasttid] != tid
    cookies[:lasttid] = tid
    Article.all(:uid.not => session[:id]).get(tid).adjust! hits: 1
  end
  @thread = Article.get(tid) or not_found
  path = "#{ATTACHMENT_DIR}/#{@thread.id}-#{@thread.attachment}"
  @size = File.readable?(path) && File.size(path)
  erb :thread
end

get '/attachment/*/*' do |tid, filename|
  session!
  @path = "#{ATTACHMENT_DIR}/#{tid}-#{filename}"
  not_found unless File.readable? @path
  send_file @path
end

get '/rss' do
  threads = Article.all order: [ :id.desc ], limit: 20
  content_type 'application/rss+xml'
  Builder::XmlMarkup.new.rss :version => '2.0' do |xml|
    xml.channel do
      xml.title 'yutar.net'
      xml.link to '/'
      xml.description 'yutar. the premium.'
      threads.each do |thread|
        xml.item do
          xml.title "[#{forum_name[thread.fid]}] #{thread.subject}"
          xml.link to "/thread/#{thread.id}"
          xml.description formattext(thread.message).gsub(/\n/, '<br/>')
          xml.author "#{'%02d' % thread.user.year}#{thread.user.name}"
          xml.pubDate thread.created_at.strftime('%a, %-d %b %Y %T %z')
          xml.category forum_name[thread.fid]
        end
      end
    end
  end
end

get '/edit_thread/forum/*' do |fid|
  session!
  @thread = Article.new fid: fid.to_i
  erb :edit_thread
end

get '/edit_thread/*' do |tid|
  session!
  @thread = Article.get(tid) or not_found
  erb :edit_thread
end

post '/edit_thread/forum/*' do |fid|
  session!
  error 204 if params[:subject] !~ /\S/
  attachment = params[:attachment]
  thread = Article.create(
    subject: params[:subject],
    message: params[:message],
    fid: fid,
    created_at: Time.now,
    uid: session[:id],
    attachment: attachment && attachment[:filename] || '',
  )
  error unless thread.saved?
  if attachment && attachment[:filename]
    store attachment[:tempfile],
      "#{ATTACHMENT_DIR}/#{thread.id}-#{attachment[:filename]}"
  end
  redirect to "/thread/#{thread.id}"
end

post '/edit_thread/*' do |tid|
  session!
  error 204 if params[:subject] !~ /\S/
  attachment = params[:attachment]
  Article.all(uid: session[:id]).get(tid).update(
    subject: params[:subject],
    message: params[:message],
    attachment: attachment && attachment[:filename] || '',
  )
  if attachment && attachment[:filename]
    store attachment[:tempfile],
      "#{ATTACHMENT_DIR}/#{tid}-#{attachment[:filename]}"
  end
  redirect to "/thread/#{tid}"
end

get '/delete_thread/*' do |tid|
  session!
  thread = Article.all(uid: session[:id]).get(tid) or not_found
  if thread.destroy
    FileUtils.rm_f "#{ATTACHMENT_DIR}/#{tid}-#{thread.attachment}"
  end
  redirect to "/forum/#{thread.fid}"
end

get '/delete_message/*' do |mid|
  session!
  Message.all(uid: session[:id]).get(mid).destroy if params[:y]
  redirect back
end

post '/message' do
  session!
  Message.create(
    tid: params[:tid],
    message: params[:message],
    created_at: Time.now,
    uid: session[:id],
  )
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

error DataObjects::SQLError do
  halt 500, 'no database connection'
end
