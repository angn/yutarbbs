# encoding=utf-8

require 'json'

module Yutarbbs
  def my
    my = Mysql.connect '127.0.0.1', 'yutar', '', 'yutar'
    my.charset = 'utf8'
    my.query "SET time_zone='+09:00'"
    my
  end

  def with_sym_keys hash
    Hash[hash.map { |k,v| [ k.to_sym, v ] }] if hash
  end

  def fetch_one query, *params
    with_sym_keys my.prepare(query).execute(*params).fetch_hash
  end

  def fetch_all query, *params
    result = []
    my.prepare(query).execute(*params).each_hash do |e|
      result << with_sym_keys(e)
    end
    result
  end

  def insert table_name, data
    my.prepare("INSERT INTO #{table_name} (#{data.keys * ','}) VALUES (#{%w/?/ * data.values.length * ','})").execute *data.values
  end

  def update table_name, where, *params, data
    sets = data.keys.map { |e| "#{e}=?" } * ','
    my.prepare("UPDATE #{table_name} SET #{sets} WHERE #{where}").execute *data.values, *params
  end

  def delete table_name, where, *params
    my.prepare("DELETE FROM #{table_name} WHERE #{where}").execute *params
  end

  def now
    Time.now.strftime '%Y%m%d%H%M%S'
  end

  def alert text
    "<script>alert(#{text.to_json});history.back()</script>"
  end

  def h text
    text.to_s.gsub(/&/, '&amp;').gsub(/</, '&lt;')
  end

  def formattext text
    text
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
    number.to_s.gsub /(?<=\d)(?=(\d\d\d)+)$/, ','
  end
  
  def u *items
    uri "/#{items * '/'}", false, false
  end

  def hashpasswd text
    Digest::MD5.hexdigest "gkfd#{text}gkfd"
  end

  def forum_name
    %w/_ 공지 자유게시판 학술 PS 유타닷넷 운영 소모임 질문·토론 진로 테크/
  end

  def replace_emoticons html
    html
  end
  
  def get_updated_forum
    rs = fetch_all 'SELECT fid FROM threads GROUP BY fid HAVING MAX(created_at) > NOW() - INTERVAL 1 DAY'
    rs2 = fetch_all 'SELECT fid FROM messages INNER JOIN threads USING (tid) GROUP BY fid HAVING MAX(messages.created_at) > NOW() - INTERVAL 1 DAY'
    (rs | rs2).map { |e| e[:fid] }
  end
  
  def is_forum_updated fid
    @@updates ||= get_updated_forum
    @@updates.include? fid
  end
  
  @@thread ||= Thread.new do
    @@updates = nil while sleep 60
  end
end
