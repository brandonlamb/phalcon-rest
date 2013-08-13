<?php

/**
 * \App\Bootstrap
 * Bootstrap.php
 *
 * Bootstraps the application
 *
 * @author Brandon Lamb
 * @since 2013-06-26
 * @category Library
 */

namespace App;

use Phalcon\Mvc\Url as PhUrl,
	Phalcon\Loader as PhLoader,
	Phalcon\Logger\Adapter\File as PhLogger,
	Phalcon\Mvc\Application as PhApplication,
	Phalcon\Db\Adapter\Pdo\Mysql as PhMysql,
	Phalcon\Mvc\Router as PhRouter,
	Phalcon\Mvc\Model\Metadata\Memory as PhMetadataMemory,
	Phalcon\Session\Adapter\Files as PhSession,
	Phalcon\Exception as PhException,
	\App\Exception\Http as HttpException;

class Bootstrap
{
	/**
	 * @var \Phalcon\DiInterface
	 */
	private $di;

	/**
	 * @var \Phalcon\Mvc\Application
	 */
	private $app;

	/**
	 * Constructor
	 *
	 * @param \Phalcon\DiInterface $di
	 */
	public function __construct($di)
	{
		// Set the di container
		$this->di = $di;

		// Include debug functions
		require_once \ROOT_PATH . '/application/config/debug.php';

		// Store config in the Di container
		$this->di->set('config', function() {
			return require_once \ROOT_PATH . '/application/config/config.php';
		}, true);
	}

	/**
	 * Runs the application performing all initializations
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function run($options = array())
	{
		try {
			$config = $this->di->getShared('config');
			$this->app = new PhApplication($this->di);

			$this->initLoader($config, $options);
			$this->initEnvironment($config, $options);
			$this->initUrl($config, $options);
			$this->initDispatcher($config, $options);
			$this->initRouter($config, $options);
			$this->initView($config, $options);
			$this->initLogger($config, $options);
			$this->initDatabase($config, $options);
			$this->initCache($config, $options);
			$this->initModules($config, $options);
			$this->initRequestBody($config, $options);

#			$response = $this->app->handle()->getContent();
			$response = $this->app->handle();
#d($response);
#$response->send();
#exit;
#d($response->send());
#			$response->send();

#			return $this->app->handle();
#			$response = $this->app->handle();
#			echo $response->send();

#			$dispatcher = $this->di->getShared('dispatcher');
#			$dispatcher->getEventsManager()->fire('dispatch:afterExcecuteRoute', $dispatcher);
#			d($response);

			#		!$this->app->request->isAjax() && bench();
		} catch (HttpException $e) {
			// Catch proper exceptions
#d(__LINE__);
			$e->send();
		} catch (\Exception $e) {
#d(__LINE__);
			// Catch all other exceptions and package into a new HttpException response
			$response = new HttpException($e->getMessage(), 500);
			$response->send();
		}
	}

	/**
	 * Initializes the loader
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initLoader($config, $options)
	{
		$this->di->set('loader', function() use ($config) {
			return require_once \ROOT_PATH . '/application/config/loader.php';
		}, true);

		$this->di->get('loader')->register();
	}

	/**
	 * Initializes the environment
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initEnvironment($config, $options)
	{
		$di = $this->di;
		$app = $this->app;

#		set_error_handler(array('\App\Error', 'normal'));
#		set_exception_handler(array('\App\Error', 'exception'));

		set_error_handler(function ($type, $message, $file, $line) use ($di, $app) {
			// Use logger to log error message
			$di->getShared('logger')->log("$type, $message, $file, $line");

			// Use lower level builtin logger to log error message
			error_log("$type, $message, $file, $line");
		});

		set_exception_handler(function($exception) use ($di, $app) {
			// HttpException's send method provides the correct response headers and body
			if (is_a($exception, 'App\\Exception\\HttpException')) {
				$exception->send();
			}

			// Use logger to log error message
#			$di->getShared('logger')->error($exception);
#			$di->getShared('logger')->error($exception->getTraceAsString());

			// Use lower level builtin logger to log error message
			error_log($exception);
			error_log($exception->getTraceAsString());
		});
	}

	/**
	 * Initializes the baseUrl
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initUrl($config, $options)
	{
		// The URL component is used to generate all kind of urls in the application
		$this->di->set('url', function() use ($config) {
			$url = new PhUrl();
			$url->setBaseUri($config->app->baseUri);
			return $url;
		});
	}

	/**
	 * Initializes the dispatcher
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initDispatcher($config, $options)
	{
		$this->di->set('dispatcher', function() {
			return require_once \ROOT_PATH . '/application/config/loader.php';
		});
	}

	/**
	 * Initializes the router
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	public function initRouter($config, $options)
	{
		$this->di->set('router', function() use ($config) {
			return require_once \ROOT_PATH . '/application/config/router.php';
		});
	}

	/**
	 * Initializes the view
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initView($config, $options)
	{
		$di = $this->di;
		$this->di->set('view', function() use ($config, $di) {
			return require_once \ROOT_PATH . '/application/config/view.php';
		});
	}

	/**
	 * Initializes the logger
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initLogger($config, $options)
	{
		$this->di->set('logger', function() use ($config) {
			return new PhLogger($config->logger->file, array('mode' => 'a+'));
		}, true);
	}

	/**
	 * Initializes the database and metadata adapter
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initDatabase($config, $options)
	{
		$this->di->set('db', function() use ($config) {
			$connection = new PhMysql(array(
				'host'     => $config->database->host,
				'username' => $config->database->username,
				'password' => $config->database->password,
				'dbname'   => $config->database->name,
			));

			return $connection;
		});
	}

	/**
	 * Initializes the session
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initSession($config, $options)
	{
		$this->di->set('session', function() {
			$session = new PhSession();
			$session->start();
			return $session;
		}, true);
	}

	/**
	 * Initializes the cache
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initCache($config, $options)
	{
		$this->di->set('viewCache', function() use ($config) {
			return require_once \ROOT_PATH . '/application/config/view-cache.php';
		});
	}

	/**
	 * Initializes the cache
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initModules($config, $options)
	{
		$this->app->registerModules($config->modules->toArray());
		$this->app->setDefaultModule('v1');
	}

	/**
	 * Initializes the request body parser
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	protected function initRequestBody($config, $options)
	{
		// If our request contains a body, it has to be valid JSON. This parses the body into a standard Object and makes
		// that vailable from the DI. If this service is called from a function, and the request body is nto valid JSON or is empty,
		// the program will throw an Exception.
		$this->di->set('requestBody', function() use ($config) {
			require_once $config->app->path->config . '/request-body.php';
		}, true);
	}

	/**
	 * Initializes the response
	 *
	 * @param \Phalcon\Config $config
	 * @param array $options
	 */
	public function initResponse($config, $options)
	{
		$this->di->set('response', '\\Phalcon\\Http\\Response', true);
	}
}

return new \App\Bootstrap(new \Phalcon\DI\FactoryDefault());
