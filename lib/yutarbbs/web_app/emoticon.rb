# encoding=utf-8

module Yutarbbs
  class WebApp
    get '/emoticons' do
      session!
      @emoticons = Emoticon.collection.keys
      slim :emoticons
    end

    post '/emoticons' do
      session!
      emoticon = params[:emoticon] or halt 205
      File.size(emoticon[:tempfile]) <= 100 * 1024 or
        error 400, alert('100KB 이하로 해줘요.')
      Emoticon.add emoticon[:tempfile], emoticon[:filename]
      redirect back, 303
    end

    get '/emo/*' do |name|
      filename = Emoticon.collection[name] or halt 404
      send_file filename
    end
  end
end
