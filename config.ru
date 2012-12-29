$:.unshift File.expand_path '../lib', __FILE__
ROOT = File.expand_path '..', __FILE__
require 'yutarbbs'
run Yutarbbs::WebApp
