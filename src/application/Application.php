<?php

use \Phalcon\Mvc\Url as PhUrl,
	\Phalcon\Mvc\View as PhView,
	\Phalcon\Mvc\Dispatcher as PhDispatcher,
	\Phalcon\Logger\Adapter\File as PhLogger,
	\Phalcon\Session\Adapter\Files as PhSession;

/**
 * Application class is responsible for setting up and initializing resources, services
 * and configuration, then handling the request and sending response
 */
class Application extends \Phalcon\Mvc\Application
{
	/**
	 * Entry into application. Setup autoloader and configuration, register modules and services
	 */
	public function main()
	{
		// Include debug functions
		require_once \ROOT . '/application/config/debug.php';

		// Register new Dependency Injector
		$di = $this->getDI();

		// Register config
		$di->setShared('config', function() {
			return require_once __DIR__ . '/config/config.php';
		});

		// Setup autoloading
		$this->registerAutoloaders();

		// Setup resources/services
		$this->registerServices();

		//Register the installed modules
		$this->registerModules($di->getShared('config')->modules->toArray());

		// Set default module to latest version
		$this->setDefaultModule('v1');

		return $this;
	}

	/**
	 * Register autoloading of namespaces, directories and classes
	 */
	protected function registerAutoloaders()
	{
		$config = $this->di->getShared('config');
		$this->di->setShared('loader', function() use ($config) {
			return require_once \ROOT . '/application/config/loader.php';
		});

		$this->loader->register();
	}

	/**
	 * This methods registers the services to be used by the protectedlication
	 */
	protected function registerServices()
	{
		$di = $this->di;
		$config = $this->config;

		// Registering a router
		$di->setShared('router', function() use ($config) {
			return require_once \ROOT . '/application/config/router.php';
		});

		// The URL component is used to generate all kind of urls in the protectedlication
		$di->setShared('url', function() use ($config) {
			$url = new PhUrl();
			$url->setBaseUri($config->phalcon->baseUri);
			return $url;
		});

		// Registering a dispatcher
		$di->setShared('dispatcher', function() use ($config) {
			$dispatcher = new PhDispatcher();
			$dispatcher->setDefaultNamespace($config->phalcon->defaultNamespace);
			$dispatcher->setDefaultController($config->phalcon->defaultController);
			$dispatcher->setDefaultAction($config->phalcon->defaultAction);

			return $dispatcher;
		});

		// Registering the view component
		$di->setShared('view', function() {
			$view = new PhView();
			$view->setViewsDir(\ROOT . '/application/views/');
			$view->setMainView('index');

			return $view;
		});

		// Registering logger
		$di->setShared('logger', function () use ($config) {
			return new PhLogger(
				\ROOT . $config->phalcon->logsDir . 'application-' . strftime('%Y-%m-%d') . '.log',
				array('mode' => 'a+')
			);
		});

		// As soon as we request the session service, it will be started.
		$di->setShared('session', function(){
			$session = new PhSession();
			$session->start();

			return $session;
		});

		// If our request contains a body, it has to be valid JSON.  This parses the body into a standard Object and makes
		// that vailable from the DI.  If this service is called from a function, and the request body is nto valid JSON or is empty,
		// the program will throw an Exception.
		$di->setShared('requestBody', function() {
			require_once \PATH . '/application/config/request-body.php';
		});
	}

	/**
	 * Override parent to handle try/catch of errors
	 */
	public function run()
	{
		d('here');

		try {
			parent::run();
		} catch (\Exception $e) {
			die('Here: ' . $e->getMessage());
		}
	}
}

return new Application(new \Phalcon\DI\FactoryDefault());
