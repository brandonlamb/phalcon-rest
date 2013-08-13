<?php

namespace App\Route;

use Phalcon\Mvc\Router\Group as RouterGroup;

class GenericGroup extends RouterGroup
{
    /**
     * @var string, resource group path in uri to search for
     */
    protected $resourceGroup;

    /**
     * Sub-namespace to use for this resource group
     */
    protected $ns;

    /**
     * @param string $resourceGroup, what to search the uri for
     * @param string @ns, the sub-namespace to use
     */
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

        // Add instance route to the group
        foreach (array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS') as $method) {
            $this->add(
                '/:module/' . $this->resourceGroup . '/:controller/:action/:params',
                array(
                    'namespace' => 'Api\\Controller\\' . $this->ns . '\\Instance',
                    'module' => 1,
                    'controller' => 2,
                    'action' => $method,
                    'params' => 4,
                    'id' => 3,
                )
            )->setHttpMethods($method);
        }
    }
}
