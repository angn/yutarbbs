module Yutarbbs
  require 'yutarbbs/database'
  require 'yutarbbs/text'
  
  def hashpasswd text
    Digest::MD5.hexdigest "gkfd#{text}gkfd"
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
  
  def store src, dest
    FileUtils.cp src, dest
    FileUtils.chmod 0666, dest
  end
end
