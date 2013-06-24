<?php

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