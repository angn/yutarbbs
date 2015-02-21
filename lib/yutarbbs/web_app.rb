require 'sinatra/base'
require 'slim'
require 'sass/plugin/rack'

Encoding.default_external = Encoding::UTF_8

module Yutarbbs
  class WebApp < Sinatra::Base
    set :dump_errors, development?
    enable :layout
    set :views, "#{ROOT}/views"
    set :public_folder, "#{ROOT}/public"

    use Rack::Session::Cookie,
      key: 'yutarbbs',
      secret: __FILE__

    use Sass::Plugin::Rack
    Sass::Plugin.options.update style: :compressed, cache: false

    helpers Helpers
    helpers Text
    helpers Emoticon

    before do
      env['rack.session.options'][:expire_after] =
        request.cookies[:keeplogin] ? 14 * 86400 : nil
    end

    error DataObjects::SQLError do
      content_type 'text/plain;charset=utf-8'
      halt 500, "DATABASE ERROR\n--------------\n#{env['sinatra.error'].message}"
    end

    error do
      content_type 'text/plain;charset=utf-8'
      halt 500, "#{env['sinatra.error'].name}\n\n#{env['sinatra.error'].message}"
    end

    get '/' do
      if session[:id]
        if notice = Article.last(fid: 1)
          redirect "/thread/#{notice.id}", 303
        else
          redirect "/forum/1", 303
        end
      end
      slim :index, layout: :new
    end
  end
end

require 'yutarbbs/web_app/user'
require 'yutarbbs/web_app/article'
require 'yutarbbs/web_app/reply'
require 'yutarbbs/web_app/emoticon'
