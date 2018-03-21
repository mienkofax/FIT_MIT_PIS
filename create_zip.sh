#!/usr/bin/env bash

/bin/rm -rf temp/cache
/bin/rm -rf temp/proxies

/bin/rm -rf log/error.log
/bin/rm -rf log/exception*

zip -r xlogin00.zip index.php doc.html example_import.json doctrine_devel.sql \
	composer.json .htaccess www tests bin app log temp
