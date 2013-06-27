<?php

return new \Phalcon\Config(array(
	'app' => array(
		'baseUri'			=> '/',
		'cache'				=> array(
			'cacheDir'		=> '/app/var/cache/',
			'lifetime'		=> 86400,
		),
		'defaultNamespace'	=> '',
		'defaultModule'		=> 'v1',
		'defaultController'	=> 'index',
		'defaultAction'		=> 'get',
		'env'				=> array('dev' => true),
		'path'				=> array(
			'config'		=> \ROOT_PATH . '/application/config',
			'controllers'	=> \ROOT_PATH . '/application/controllers',
			'library'		=> \ROOT_PATH . '/application/library',
			'logs'			=> \ROOT_PATH . '/application/logs',
			'models'		=> \ROOT_PATH . '/application/models',
			'plugins'		=> \ROOT_PATH . '/application/plugins',
			'views'			=> \ROOT_PATH . '/application/views',

		),
		'volt'				=> array(
			'path'			=> \ROOT_PATH . '/application/cache/volt',
			'extension'		=> '.php',
			'separator'		=> '%%',
			'stat'			=> 1,
		),
	),

	'database' => array(
		'adapter'	=> 'db2',
		'host'		=> 'dsn',
		'username'	=> 'appphp',
		'password'	=> 'prodpw',
		'name'		=> 'db2_rw'
	),

	'logger' => array(
		'file' => \ROOT_PATH . '/application/runtime/logs/application-' . strftime('%Y-%m-%d') . '.log',
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
			'path' => \ROOT_PATH . '/application/modules/v1/Module.php'
		),

		'v2' => array(
			'className' => 'Api\\Module',
			'path' => \ROOT_PATH . '/application/modules/v2/Module.php'
		),
	),
));
