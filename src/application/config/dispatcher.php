<?php

$evManager = new \Phalcon\Events\Manager();
$evManager->attach('dispatch:beforeException', function($event, $dispatcher, $exception) {
	switch ($exception->getCode()) {
		case \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
		case \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
			$dispatcher->forward(array(
				'controller' => 'index',
				'action' => 'show404',
			));
			return false;
	}
});

$dispatcher = new \Phalcon\Mvc\Dispatcher();
$dispatcher->setDefaultNamespace($config->app->defaultNamespace);
$dispatcher->setDefaultController($config->app->defaultController);
$dispatcher->setDefaultAction($config->app->defaultAction);
$dispatcher->setEventsManager($evManager);

return $dispatcher;
