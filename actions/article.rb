get '/' do
  @notice = Article.last fid: 1
  redirect to "/thread/#{@notice.id}" if session?
  erb :index
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
