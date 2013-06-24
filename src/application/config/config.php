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
		'logFile'			=> \ROOT . '/application/runtime/application.log',
	),

	'models' => array(
		'metadata' => array(
			'adapter'	=> 'Apc',
			'lifetime'	=> 86400
		)
	),

	'modules' => array(
		'v1' => array(
			'className' => 'Api\\Module',
			'path' => \ROOT . '/application/modules/v1/Module.php'
		),
		
		'v2' => array(
			'className' => 'Api\\Module',
			'path' => \ROOT . '/application/modules/v2/Module.php'
		),
	),
));
