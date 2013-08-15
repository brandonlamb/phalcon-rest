<?php

// Get the parameters
$frontCache = new \Phalcon\Cache\Frontend\Output(array(
    'lifetime' => $config->app->cache->lifetime,
));

if (function_exists('apc_store')) {
    $cache = new \Phalcon\Cache\Backend\Apc($frontCache);
} else {
    $cache = new \Phalcon\Cache\Backend\File(
        $frontCache,
        array('cacheDir' => \ROOT_PATH . $config->app->cache->cacheDir)
    );
}

return $cache;
