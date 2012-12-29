require 'sinatra/base'
require 'sinatra/reloader'
require 'sinatra/session'
require 'sinatra/cookies'
require 'rack/utils'

Encoding.default_external = Encoding::UTF_8

module Yutarbbs
  class WebApp < Sinatra::Base
    register Sinatra::Reloader if development?
    register Sinatra::Session

    disable :dump_errors if development?
    enable :logging
    enable :layout
    set :views, "#{ROOT}/views"
    set :public_folder, "#{ROOT}/public"
    set :session_fail, '/'
    set :session_name, 'yutarbbs'
    set :session_secret, __FILE__

    helpers Rack::Utils
    helpers Sinatra::Cookies
    helpers do
      alias_method :h, :escape_html
    end
    helpers Helpers
    helpers Text

    before do
      env['rack.session.options'][:expire_after] =
        cookies[:keeplogin] ? 14 * 86400 : nil
    end

    not_found do
      ''
    end

    error DataObjects::SQLError do
      content_type 'text/plain;charset=utf-8'
      halt 500, "DATABASE ERROR\n--------------\n#{env['sinatra.error'].message}"
    end

    error do
      content_type 'text/plain;charset=utf-8'
      halt 500, "#{env['sinatra.error'].name}\n\n#{env['sinatra.error'].message}"
    end
  end
end

require 'yutarbbs/web_app/user'
require 'yutarbbs/web_app/article'
require 'yutarbbs/web_app/reply'
require 'yutarbbs/web_app/emoticon'
