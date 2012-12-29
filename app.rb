require 'sinatra'
require 'sinatra/reloader' if development?
require 'sinatra/session'
require "sinatra/cookies"
require 'rack/utils'
require 'yutarbbs'
require 'builder'
also_reload 'lib/yutarbbs' if development?
also_reload 'lib/yutarbbs/text' if development?
also_reload 'lib/yutarbbs/model' if development?

raise "ATTACHMENT_DIR is not set." unless ENV['ATTACHMENT_DIR']
ATTACHMENT_DIR = File.expand_path ENV['ATTACHMENT_DIR']
raise "#{ATTACHMENT_DIR} doesn't exist." unless File.directory? ATTACHMENT_DIR

raise "EMOTICON_DIR is not set." unless ENV['EMOTICON_DIR']
EMOTICON_DIR = File.expand_path ENV['EMOTICON_DIR']
raise "#{EMOTICON_DIR} doesn't exist." unless File.directory? EMOTICON_DIR

Encoding.default_external = Encoding::UTF_8

disable :dump_errors
enable :layout
set :session_name, 'yutarbbs'
set :session_secret, "#{__FILE__}#{ATTACHMENT_DIR}#{EMOTICON_DIR}"

helpers Rack::Utils, Yutarbbs, Yutarbbs::Text
helpers do
  alias_method :h, :escape_html
end
include Yutarbbs::Model

before do
  env['rack.session.options'][:expire_after] =
    cookies['keeplogin'] ? 60 * 60 * 24 * 14 : nil
end

error DataObjects::SQLError do
  halt 500, 'no database connection'
end

load 'actions/user.rb'
load 'actions/article.rb'
load 'actions/edit.rb'
load 'actions/reply.rb'
load 'actions/emoticon.rb'
