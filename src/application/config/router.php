<?php

$router = new \Phalcon\Mvc\Router();

//Remove trailing slashes automatically
$router->removeExtraSlashes(true);

// Set the URI source
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_GET_URL);
#$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

// Set default router options
#$router->setDefaultModule($config->app->defaultModule);
$router->setDefaults(array(
	'module' => $config->app->defaultModule,
	'controller' => $config->app->defaultController,
	'action' => $config->app->defaultAction,
));

// Add REST API matches for each HTTP Method
foreach (array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS') as $method) {
	$router->add(
		'/:module/:controller/:params',
		array(
			'module' => 1,
			'controller' => 2,
			'action' => $method,
			'params' => 3,
		)
	)->setHttpMethods($method);

	$router->add(
		'/:module/:controller/:int/:controller/:params',
		array(
			'module' => 1,
			'controller' => 2,
			'action' => strtolower($method),

			'params' => 3,
		)
	)->setHttpMethods($method);
}

$router->mount(new \App\Route\GenericGroup('location-group', 'LocationGroup'));

// Set 404 paths
$router->notFound(array(
	'module' => 'v1',
	'controller' => 'error',
	'action' => 'notfound',
));

return $router;
