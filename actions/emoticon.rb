# encoding=utf-8

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
