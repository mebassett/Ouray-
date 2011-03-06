<?php
        define('db_host', 			'');					//MySQL database server
        define('db_name', 			'');				//MySQL database name
        define('db_user', 			'');						//MySQL user
        define('db_pass', 			'');						//Password
        define('tPref'	, 			'');					//table prefix
        define('fileUpload',		'./uploads/');
        define('uploadUrlPrefix',	'/files/?q=');
	define('facebookAPIID',		'');

        define('facebookKEY',		'');
        
        define('db_ConnectionString', "mysql:host=".db_host.";dbname=".db_name.";unix_socket=/var/lib/mysql/mysql.sock");
?>
