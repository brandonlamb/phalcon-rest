<?php

namespace Api;

use \Phalcon\Mvc\ModuleDefinitionInterface as ModuleInterface,
    \Phalcon\DiInterface as DiInterface,
    \Phalcon\Mvc\View as PhView,
    \Phalcon\Mvc\Dispatcher as PhDispatcher,
    \Phalcon\Events\Manager as PhEventsManager;

class Module implements ModuleInterface
{
    public function registerAutoloaders()
    {
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces(array(
            'Api\\Controller' => \ROOT_PATH . '/application/modules/v1/controller/',
            'Api\\Model' => \ROOT_PATH . '/application/modules/v1/model/',
        ));
        $loader->register();
    }

    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    public function registerServices($di)
    {
        // Register the dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new PhDispatcher();

            // Attach a event listener to the dispatcher
            $eventManager = new PhEventsManager();
#           $eventManager->attach('dispatch', new \Acl('frontend'));

            $dispatcher->setEventsManager($eventManager);
            $dispatcher->setDefaultNamespace('Api\\Controller\\');

            return $dispatcher;
        }, true);

        // Register the view component
        $di->set('view', function () {
            $view = new PhView();
            $view->setViewsDir(\ROOT_PATH . '/application/modules/v1/views/');

            // Use a shared view directory
#           $view->setMainView('../../views/layouts/index');
            $view->setMainView('index');

            return $view;
        }, true);
    }
}
