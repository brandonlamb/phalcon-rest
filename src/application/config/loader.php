<?php

$loader = new \Phalcon\Loader();

// Register namespaces
$loader->registerNamespaces(array(
	'Api' => \ROOT_PATH . '/application/vendor/Api',
	'App' => \ROOT_PATH . '/application/library/App',
	'CacheCache' => \ROOT_PATH . '/application/vendor/CacheCache/src/CacheCache',
	'PHPExcel' => \ROOT_PATH . '/application/vendor/PHPExcel/Classes',
	'PHPMailer' => \ROOT_PATH . '/application/vendor/PHPMailer/Classes',
	'Sbux' => \ROOT_PATH . '/application/vendor/Sbux/src/Sbux',
	'Spot' => \ROOT_PATH . '/application/vendor/Spot/src/Spot',

	'PhalconRest\Models' => __DIR__ . '/models',
	'PhalconRest\Controllers' => __DIR__ . '/controllers',
	'PhalconRest\Exceptions' => __DIR__ . '/exceptions',
	'PhalconRest\Responses' => __DIR__ . '/responses',
))->register();

// Register non-module directories
$loader->registerDirs(array(
	$config->app->path->controllers,
	$config->app->path->models,
	$config->app->path->library,
));

// Register specific classes
$loader->registerClasses(array(
	'UIElements' => \ROOT_PATH . '/application/vendor/UIElements/UIElements.php'
));

return $loader;
