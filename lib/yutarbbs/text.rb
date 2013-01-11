# encoding=utf-8

require 'json'
require 'cgi'
require 'rack/utils'

module Yutarbbs::Text
  def alert text
    "<script>alert(#{text.to_json});history.back()</script>"
  end

  def formattext text
    chunks = text.split %r{(<[a-z]+[^>]*>|</[a-z]+[^>]*>)}i
    text = chunks.map { |e| _formattexteach e } * ''
    text.sub %r{<tex>(.+?)</tex>}m do |e|
      %Q{<img src="http://www.forkosh.dreamhost.com/mathtex.cgi?#{CGI.escape e}">}
    end
  end

  def _formattexteach text
    return text if text =~ %r{^<[A-Z/].*>$}i
    Rack::Utils.escape_html(text).gsub %r{\b(https?://[^\s<]+)|([\w.]+@[\w.]+)|(?<!\w)@((?>[\w]+))(?!@)} do
      style = nil
      if $1 # website
        href, label = $1, $1
        # $label = iconv('utf8', 'utf8//translit', rawurldecode($website));
      elsif $2 # email
        href, label = "mailto:#{$2}", $2
      elsif $3 # twitter
        href, label, style = "https://twitter.com/#{$3}", "@#{$3}", 'twit'
      end
      %Q{<a target="_blank" href="#{href}" class="#{style}">#{label}</a>}
    end
  end

  def formatphone text
    text.gsub /^(01[01679])(\d{3,4})(\d{4})$/, '\1-\2-\3'
  end

  def formatdate timestamp, with_time = false
    date = Time.at timestamp
    now = Time.now
    html = if now - date <= 5 * 60
      '<em>방금</em>'
    elsif date >= Time.local(now.year, now.month, now.day)
      '<em>오늘</em>'
    elsif date >= Time.local(now.year, now.month, now.day - 1)
      '어제'
    elsif date >= Time.local(now.year, now.month, now.day - 2)
      '그제'
    elsif date >= Time.local(now.year)
      date.strftime '%-m/%-d'
    else
      date.strftime "'%y %-m/%-d"
    end
    html << " #{date.strftime '%R'}" if with_time
    html
  end

  def formattime timestamp
    formatdate timestamp, true
  end

  def number_format number
    number.to_s.gsub /(?<=\d)(?=(\d\d\d)+$)/, ','
  end
  
  def forum_name
    %w/_ 공지 자유게시판 학술 PS 유타닷넷 운영 소모임 질문·토론 진로 테크/
  end
end
