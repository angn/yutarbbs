Yutarbbs
========

Who would have interest of this?


Development
-----------

You're using Ubuntu 12.04, aren't you? I am.

1. sudo apt-get install ruby1.9
2. sudo gem1.9 install bundler
3. bundle install --without=production
4. rackup
5. curl -i localhost:9292


Notes
-----

* In development env, "tester" account is created. (no password)
* On windows, the sqlite dll is required in ruby bin directory.
  (Visit http://www.sqlite.org/download.html)
* Recommend using RVM rather than the system ruby.
* Doesn't work on ruby 1.8
