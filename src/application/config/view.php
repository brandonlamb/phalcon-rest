<?php

$view = new \Phalcon\Mvc\View();
$view->setViewsDir(\ROOT_PATH . $config->app->path->views);
$view->registerEngines(array(
	'.volt' => function($view, $di) use ($config) {
		$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
		$volt->setOptions(
			array(
				'compiledPath'		=> \ROOT_PATH . $config->app->volt->path,
				'compiledExtension' => $config->app->volt->extension,
				'compiledSeparator' => $config->app->volt->separator,
				'stat'				=> (bool) $config->app->volt->stat,
			)
		);
		return $volt;
	};
));
return $view;
