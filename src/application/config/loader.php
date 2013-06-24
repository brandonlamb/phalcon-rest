<?php

$loader = new \Phalcon\Loader();

// Register namespaces
$loader->registerNamespaces(array(
	'Api' => \ROOT . '/application/vendor/Api/',
	'CacheCache' => \ROOT . '/application/vendor/CacheCache/src/CacheCache/',
	'PhalconRest\Models' => __DIR__ . '/models/',
	'PhalconRest\Controllers' => __DIR__ . '/controllers/',
	'PhalconRest\Exceptions' => __DIR__ . '/exceptions/',
	'PhalconRest\Responses' => __DIR__ . '/responses/',
	'PHPExcel' => \ROOT . '/application/vendor/PHPExcel/Classes/',
	'PHPMailer' => \ROOT . '/application/vendor/PHPMailer/Classes/',
	'Sbux' => \ROOT . '/application/vendor/Sbux/src/Sbux/',
	'Spot' => \ROOT . '/application/vendor/Spot/src/Spot/',
))->register();

// Register non-module directories
$loader->registerDirs(array(
	$config->phalcon->controllersDir,
	$config->phalcon->modelsDir,
	$config->phalcon->pluginsDir,
));

// Register specific classes
$loader->registerClasses(array(
	'UIElements' => \ROOT . '/application/vendor/UIElements/UIElements.php'
));

return $loader;
