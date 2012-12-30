# encoding=utf-8

module Yutarbbs
  class WebApp
    EMOTICON_DIR = File.expand_path ENV['EMOTICON_DIR'] || 'emo'
    raise "#{EMOTICON_DIR} doesn't exist." unless File.directory? EMOTICON_DIR

    get '/emoticons' do
      session!
      @emoticons = Dir.entries(EMOTICON_DIR)
      @emoticons.delete_if { |e| e[0] == '.' }
      @emoticons.each { |e| e.encode!('utf-8').sub! /\.[^.]*$/, '' }
      erb :emoticons
    end

    post '/emoticons' do
      session!
      emoticon = params[:emoticon] or halt 205
      File.size(emoticon[:tempfile]) <= 100 * 1024 or
        halt 400, alert('100KB 이하로 해줘요.')
      store emoticon[:tempfile], "#{EMOTICON_DIR}/#{emoticon[:filename]}"
      redirect back
    end

    IMAGE_EXT = %w/jpg JPG jpeg JPEG gif GIF png PNG/

    get '/emo/*' do |name|
      candidates = IMAGE_EXT.map { |ext| "#{EMOTICON_DIR}/#{name}.#{ext}" }
      filename = candidates.find { |e| File.readable? e } or halt 404
      send_file filename
    end
  end
end
