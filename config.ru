$:.unshift File.expand_path '../lib', __FILE__
require './app.rb'
run Sinatra::Application
