require 'mysql'

module Yutarbbs::Database
  def connect
    my = Mysql.connect '127.0.0.1', 'yutar', '', 'yutar'
    my.charset = 'utf8'
    my.query "SET time_zone='+09:00'"
    my
  end
  
  def my
    @@my ||= connect
  end
  
  def query query, *params
    my.prepare(query).execute(*params)
  rescue Mysql::ClientError::ServerGoneError
    @@my = nil
    retry
  end

  def with_sym_keys hash
    Hash[hash.map { |k,v| [ k.to_sym, v ] }] if hash
  end

  def fetch_one query, *params
    with_sym_keys query(query, *params).fetch_hash
  end

  def fetch_all query, *params
    result = []
    query(query, *params).each_hash do |e|
      result << with_sym_keys(e)
    end
    result
  end

  def insert table_name, data
    query("INSERT INTO #{table_name} (#{data.keys * ','}) VALUES (#{%w/?/ * data.values.length * ','})", *data.values).insert_id
  end

  def update table_name, where, *params, data
    if data.is_a? Hash
      sets = data.keys.map { |e| "#{e}=?" } * ','
      query "UPDATE #{table_name} SET #{sets} WHERE #{where}", *data.values, *params
    else
      query "UPDATE #{table_name} SET #{data} WHERE #{where}", *params
    end
  end

  def delete table_name, where, *params
    query "DELETE FROM #{table_name} WHERE #{where}", *params
  end

  def now
    Time.now.strftime '%Y%m%d%H%M%S'
  end
end
