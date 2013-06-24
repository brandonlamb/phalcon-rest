<?php

require_once '/web/library/Eve-Framework/src/Eve/autoload.php';
require_once '/web/library/Spot/src/Spot/autoload.php';

use Eve\Mvc\AbstractApplication,
	Eve\Di\DiInterface,
	Eve\Di\FactoryDefault,
	Eve\Autoload\Loader as AutoLoader;

class Application extends AbstractApplication
{
	public function init()
	{
		// Create new auto loader object and register namespaces, dirs, classes
#		require_once '/web/library/Eve-Framework/src/Eve/Loader.php';
		$loader = new AutoLoader();
		$loader->addNamespaces(array(
#			'Eve'		=> array('/web/library/Eve-Framework/src/Eve'),
		))->register();

		// Register new Dependency Injector
		$di = new FactoryDefault();
		$this->setDI($di);

		// Set the loader in the di container
		$di->setShared('loader', $loader);

		// Set config object
		$di->set('config', function () {
			return new \Eve\Mvc\Config(\PATH . '/protected/config/main.php');
		});

		$di->set('logger', function () {
			return new \Eve\Log(\PATH . '/protected/logs/application-' . strftime('%Y-%m-%d') . '.log');
		});

		// Registering the view component
		$di->set('view', function() {
			$view = new \Phalcon\Mvc\View();
			$view->setViewsDir(\PATH . '/protected/views/');
			$view->setMainView('layouts/default');
			return $view;
		});


		/*
		//Register the installed modules
		$this->registerModules(array(
			'main' => array(
				'className' => 'Main\Module',
				'path' => \PATH . '/protected/modules/Main/Module.php'
			),

			'test' => array(
				'className' => 'Test\Module',
				'path' => \PATH . '/protected/modules/Test/Module.php'
			),
		));
		*/

		$this->initAutoloader($di);
		$this->initServices($di);
		$this->initEnvironment($di);
		$this->initModules($di);

#\d($di->getShared('router'));

		return $this;
	}

	/**
	 * Registers namespaces with autoloader
	 * @param DiInterface $di
	 */
	protected function initAutoloader(DiInterface $di)
	{
		$config = $di->getShared('config')->autoloader;
		$loader = $di->getShared('loader');

		isset($config['namespaces']) && $loader->addNamespaces($config['namespaces']);
		isset($config['dirs']) && $loader->addDirs($config['dirs']);
	}

	/**
	 * This methods registers the services to be used by the protectedlication
	 * @param DiInterface $di
	 */
	protected function initServices(DiInterface $di)
	{
		// Registering a dispatcher
		$di->set('dispatcher', function() use ($di) {
			static $dispatcher;
			if (null !== $dispatcher) {
				return $dispatcher;
			}

			$config = $di->getShared('config')->app;

			$dispatcher = new \Eve\Mvc\Dispatcher();
			$dispatcher->setDefaultNamespace($config['defaultNamespace']);
			$dispatcher->setDefaultController($config['defaultController']);
			$dispatcher->setDefaultAction($config['defaultAction']);

#			$security = new \Clarity\Plugin\Security($di);
#			$eventsManager = $di->getShared('eventsManager');
#			$eventsManager->attach('dispatch', $security);
#			$dispatcher->setEventsManager($eventsManager);

			return $dispatcher;
		});
	}

   /**
	 * Initializes the environment
	 *
 	 * @param DiInterface $di
	 */
	protected function initEnvironment(DiInterface $di)
	{
#		set_error_handler(array('\Game\Error', 'normal'));
#		set_exception_handler(array('\Game\Error', 'exception'));
	}

   /**
	 * Initializes modules
	 *
 	 * @param DiInterface $di
	 */
	public function initModules(DiInterface $di)
	{
		require_once \PATH . '/protected/config/routes.php';
	}
}
