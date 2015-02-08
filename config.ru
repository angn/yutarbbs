$:.unshift File.expand_path '../lib', __FILE__
ROOT = File.expand_path '..', __FILE__
require 'yutarbbs'
use Rack::Deflater, include: %w[ text/html text/css ]
run Yutarbbs::WebApp
