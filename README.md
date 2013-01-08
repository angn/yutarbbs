Yutarbbs
========

Who would have interest of this?


Development
-----------

You're using Ubuntu 12.04, aren't you? I am.

1. sudo apt-get install ruby1.9
2. gem1.9 install bundler
3. bundle install --without=production
4. ATTACHMENT_DIR=. EMOTICON_DIR=. rackup


Notes
-----

* On windows, the sqlite dll is required in ruby bin directory.
  (Visit http://www.sqlite.org/download.html)
* Recommend using RVM rather than the system ruby.
* Doesn't work on ruby 1.8
