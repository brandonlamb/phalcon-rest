<?php
// Overhead: 0.34, 0.00034, 654K
define('START_TIME', microtime(true));
define('DOCROOT', dirname(__FILE__));
define('ROOT', dirname(DOCROOT));

try {
	// Get an Application instance
	$app = require_once \ROOT . '/application/Application.php';
	$app->main();
	
	// If the application throws an HTTPException, send it on to the client as json. Elsewise, just log it
	set_exception_handler(function($exception) use ($app) {
		// HTTPException's send method provides the correct response headers and body
		if (is_a($exception, 'PhalconRest\\Exceptions\\HTTPException')) {
			$exception->send();
		}
		error_log($exception);
		error_log($exception->getTraceAsString());
	});

	$app->handle();
	!$app->request->isAjax() && bench();
} catch (\Exception $e) {
	echo '<pre>FATAL: ', $e->getMessage(), dump($e), '</pre>';
}
