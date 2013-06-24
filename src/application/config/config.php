<?php

return new \Phalcon\Config(array(
	'database' => array(
		'adapter'	=> 'db2',
		'host'		=> 'dsn',
		'username'	=> 'appphp',
		'password'	=> 'prodpw',
		'name'		=> 'db2_rw'
	),

	'phalcon' => array(
		'controllersDir'	=> '/application/controllers',
		'modelsDir'			=> '/application/models',
		'viewsDir'			=> '/application/views',
		'pluginsDir'		=> '/application/plugins',
		'logsDir'			=> '/application/logs',
		'baseUri'			=> '/',
		'defaultNamespace'	=> '',
		'defaultModule'		=> 'v1',
		'defaultController'	=> 'index',
		'defaultAction'		=> 'index',
		'logFile'			=> \PATH . '/application/runtime/application.log',
	),

	'models' => array(
		'metadata' => array(
			'adapter'	=> 'Apc',
			'lifetime'	=> 86400
		)
	),
));
