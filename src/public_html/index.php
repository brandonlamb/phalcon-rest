<?php
// Overhead: 0.34, 0.00034, 654K
define('START_TIME', microtime(true));
define('DOCROOT', dirname(__FILE__));
define('ROOT', dirname(DOCROOT));

try {
	!defined('ROOT_PATH') && define('ROOT_PATH', dirname(dirname(__FILE__)));
	require_once ROOT_PATH . '/application/library/App/Error.php';

	// Using require once because I want to get the specific bootloader class here.
	$app = require_once ROOT_PATH . '/application/library/App/Bootstrap.php';
	echo $app->run();
} catch (\Exception $e) {
	echo '<pre>FATAL: ', $e->getMessage(), dump($e), '</pre>';
}
