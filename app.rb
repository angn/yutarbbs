require 'sinatra'
require 'sinatra/reloader' if development?
require 'mysql'
require './lib/yutarbbs'
also_reload './lib/yutarbbs.rb' if development?

include Yutarbbs

set :layout, true

get '/' do
  @notice = fetch_one 'threads WHERE fid = 1 ORDER BY created_at DESC'
  erb :index
end
