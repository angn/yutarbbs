.PHONY: make migration set-permissons make-httpd-conf merge-bootstrap
make:                  set-permissons
migration:                            make-httpd-conf merge-bootstrap
set-permissons:
	chmod 700 bin/php-pp
	chmod 777 tmp www/attachments www/bombs www/emo www/img/h2
make-httpd-conf:
	for d in `find www -name '.htaccess' -printf '%h\n'`; do echo \<Directory `realpath -z "$$d"`\>; cat "$$d/.htaccess"; echo \</Directory\>; done > var/httpd.conf
merge-bootstrap:
	[ ! -s var/boot.php ]
	cp www/boot.php var/boot.php
	bin/php-pp var/boot.php > www/boot.php
