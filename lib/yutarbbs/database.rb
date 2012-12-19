require 'mysql'

module Yutarbbs::Database
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
    puts "UPDATE #{table_name} SET #{sets} WHERE #{where}"
    my.prepare("UPDATE #{table_name} SET #{sets} WHERE #{where}").execute *data.values, *params
  end

  def delete table_name, where, *params
    my.prepare("DELETE FROM #{table_name} WHERE #{where}").execute *params
  end

  def now
    Time.now.strftime '%Y%m%d%H%M%S'
  end
end
