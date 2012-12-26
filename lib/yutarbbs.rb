module Yutarbbs
  require 'yutarbbs/model'
  require 'yutarbbs/text'
  
  def hashpasswd text
    Digest::MD5.hexdigest "gkfd#{text}gkfd"
  end

  def get_updated_forum
    in_a_day = Time.now - 24 * 60 * 60
    rs = Article.all(fields: [ :fid ], unique: true, :created_at.gt => in_a_day) +
      Message.all(:created_at.gt => in_a_day).articles(fields: [ :fid ])
    rs.map &:fid
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
