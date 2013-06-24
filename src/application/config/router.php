<?php

$router = new \Phalcon\Mvc\Router();
$router->setDefaultModule($config->phalcon->defaultModule);

// Add REST API matches for each HTTP Method
foreach (array('GET', 'POST', 'PUT', 'HEAD', 'OPTIONS', 'DELETE', 'PATH') as $method) {
	$router->add(
		'/:module/:controller/:params',
		array(
			'module' => 1,
			'controller' => 2,
			'params' => 3,
			'action' => strtolower($method),
		)
	)->setHttpMethods($method);

	$router->add(
		'/:version/:resource/:id1/:relation/:params',
		array(
			'module' => 1,
			'controller' => 2,
			'params' => 3,
			'action' => strtolower($method),
		)
	)->setHttpMethods($method);
}

return $router;
