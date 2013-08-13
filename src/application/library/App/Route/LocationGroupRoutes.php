<?php

namespace App\Route;

use Phalcon\Mvc\Router\Group as RouterGroup;

class LocationGroupRoutes extends RouterGroup
{
    protected $resourceGroup;
    protected $ns;

    public function __construct($resourceGroup, $ns)
    {
        $this->resourceGroup = (string) $resourceGroup;
        $this->ns = (string) $ns;
        parent::__construct();
    }

    public function initialize()
    {
        // Add collection route to the group
        $this->addGet(
            '/:module/' . $this->resourceGroup . '/:controller/:params',
            array(
                'namespace' => 'Api\\Controller\\' . $this->ns . '\\Collection',
                'module' => 1,
                'controller' => 2,
                'action' => 'get',
                'params' => 3,
            )
        );
    }
}
