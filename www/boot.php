<?php

error_reporting(E_ALL ^ E_NOTICE);

ini_set('mysql.default_host',     localhost);
ini_set('mysql.default_user',     yutar);
ini_set('mysql.default_password', '');
define('DATABASE', yutar);

chdir('..');
#include ./lib/*.php
#include ./mod/*.php
#include ./app/controllers/*.php
#remove
array_map(create_function('$s', 'require_once $s;'),
          array_merge(glob('./lib/*.php'),
                      glob('./mod/*.php'),
                      glob('./app/controllers/*.php')));
#endremove
die(call_user_func_array(main, array_slice(explode('/', strtok($_SERVER[REQUEST_URI], '?')), 1)));
