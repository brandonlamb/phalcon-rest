<?php

use \Phalcon\Loader as PhLoader,
	\Phalcon\Mvc\Url as PhUrl,
	\Phalcon\Mvc\View as PhView,
	\Phalcon\Mvc\Dispatcher as PhDispatcher,
	\Phalcon\Logger\Adapter\File as PhLogger,
	\Phalcon\Session\Adapter\Files as PhSession;

class Application extends \Phalcon\Mvc\Application
{
	/**
	 * Entry into application. Setup autoloader and configuration, register modules and services
	 */
	public function main()
	{
		// Register new Dependency Injector
		$di = $this->getDI();

		// Register config
		$di->setShared('config', function() {
			return require_once __DIR__ . '/config/config.php';
		});

		$this->registerAutoloaders();
		$this->registerServices();

		//Register the installed modules
		$this->registerModules(array(
			'main' => array(
				'className' => 'Main\Module',
				'path' => \PATH . '/application/main/Module.php'
			),
		));

		return $this;
	}

	/**
	 * Register autoloading of namespaces, directories and classes
	 */
	protected function registerAutoloaders()
	{
		$di = $this->getDI();

		$di->setShared('loader', function() use ($di) {
			$loader = new PhLoader();

			// Register namespaces
			$loader->registerNamespaces(array(
				'PhalconRest\Models' => __DIR__ . '/models/',
				'PhalconRest\Controllers' => __DIR__ . '/controllers/',
				'PhalconRest\Exceptions' => __DIR__ . '/exceptions/',
				'PhalconRest\Responses' => __DIR__ . '/responses/',
				'Sbux' => \PATH . '/vendor/Sbux/src/Sbux/',
			))->register();

			// Register non-module directories
			$loader->registerDirs(array(
				$config->phalcon->controllersDir,
				$config->phalcon->modelsDir,
				$config->phalcon->pluginsDir,
			));

			// Register specific classes
			$loader->registerClasses(array(
				'UIElements' => \PATH . '/vendor/UIElements/UIElements.php'
			));

			return $loader;
		});

		return $this->loader->register();
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
			return require_once \PATH . '/application/config/routes.php';
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
			$view->setViewsDir(\PATH . '/application/views/');
			$view->setMainView('index');
		
			return $view;
		});

		// Registering logger
		$di->setShared('logger', function () use ($config) {
			return new PhLogger(\PATH . $config->phalcon->logsDir . $config->phalcon->logFile, array('mode' => 'a+'));
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
			$in = json_decode(file_get_contents('php://input'), false);

			// JSON body could not be parsed, throw exception
			if ($in === null) {
				throw new HTTPException(
					'There was a problem understanding the data sent to the server by the application.',
					409,
					array(
						'dev' => 'The JSON body sent to the server was unable to be parsed.',
						'internalCode' => 'REQ1000',
						'more' => ''
					)
				);
			}

			return $in;
		});


		// Registering a Http\Response 
#		$di->set('response', function() {
#			return new \Phalcon\Http\Response();
#		});

		// Registering a Http\Request
#		$di->set('request', function() {
#			return new \Phalcon\Http\Request();
#		});

		// Registering the database component	
#		$di->setShared('db', function() {
#			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
#				"host" => "localhost",
#				"username" => "root",
#				"password" => "hea101",
#				"dbname" => "invo"
#			));
#		});

		// Registering the Models-Metadata
#		$di->set('modelsMetadata', function() {
#			return new \Phalcon\Mvc\Model\Metadata\Memory();
#		});

		// Registering the Models Manager
#		$di->set('modelsManager', function() {
#			return new \Phalcon\Mvc\Model\Manager();
#		});

#		$di->set('modelsCache', function() {
#			//Cache data for one day by default
#			$frontCache = new \Phalcon\Cache\Frontend\Data(array(
#				'lifetime' => 3600
#			));

			//File cache settings
#			$cache = new \Phalcon\Cache\Backend\File($frontCache, array(
#				'cacheDir' => __DIR__ . '/cache/'
#			));

#			return $cache;
#		});
	}
}

return new Application(new \Phalcon\DI\FactoryDefault());