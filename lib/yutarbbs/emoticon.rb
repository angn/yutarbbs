require 'fileutils'

module Yutarbbs
  module Emoticon
    puts "EMOTICON_DIR env is not found; use #{TMP_DIR} instead." unless ENV['EMOTICON_DIR']
    DIR = File.expand_path ENV['EMOTICON_DIR'] || TMP_DIR

    def self.collection
      @@emoticons ||= Hash[Dir.entries(DIR).grep(/^[^.]/).map { |e|
        e.encode! 'utf-8'
        [ e.sub(/\.[^.]*$/, ''), "#{DIR}/#{e}" ]
      }]
    end

    def self.add src, filename
      dest = "#{DIR}/#{filename}"
      FileUtils.cp src, dest
      FileUtils.chmod 0666, dest
      @@emoticons = nil
    end

    def replace_emoticons html
      html.gsub /@([^@\/.\s]+)@/ do |m|
        if Emoticon.collection[$1]
          html = Rack::Utils.escape_html $1
          %Q[<img src="/emo/#{html}" alt="#{html}">]
        end || m
      end
    end
  end
end
