require 'minitest/autorun'

module Yutarbbs; end
require_relative '../lib/yutarbbs/text'

describe Yutarbbs::Text do
  before do
    @mod = Object.new.extend Yutarbbs::Text
  end

  it '#_formattexteach should detect URLs and create links' do
    html = @mod._formattexteach 'http://a-site.net/netizen/mycomment/?cPageIndex=1&rMode=otherMy&allComment=T&userId=FrxunhuhUag0&daumName=%25EC%25A0%259C%25ED%258E%2598%25ED%2586%25A0#'
    html.must_match %r[^<a\s.*</a>]
  end
end
