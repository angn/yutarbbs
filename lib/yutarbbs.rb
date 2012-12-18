module Yutarbbs
  def my
    my = Mysql.connect '127.0.0.1', 'yutar', '', 'yutar'
    my.charset = 'utf8'
    my
  end

  def fetch_one rest_query
    my.query("SELECT * FROM #{rest_query}").fetch_hash
  end

  def h text
    text.gsub(/&/, '&amp;').gsub(/</, '&lt;')
  end

  def formattext text
    text
  end
  
  def u *items
    "/#{items * '/'}"
  end
end
