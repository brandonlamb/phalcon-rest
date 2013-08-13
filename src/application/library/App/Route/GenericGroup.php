<?php

namespace App\Route;

use Phalcon\Mvc\Router\Group as RouterGroup;

class GenericGroup extends RouterGroup
{
    /**
     * @var string, uri search path
     */
    protected $uri;

    /**
     * Sub-namespace to use for this resource group
     */
    protected $ns;

    /**
     * @param string $uri, what to search the uri for
     * @param string @ns, the sub-namespace to use
     */
    public function __construct($uri, $ns)
    {
        $this->uri = (string) $uri;
        $this->ns = (string) $ns;
        parent::__construct();
    }

    public function initialize()
    {
        // Add collection route to the group
         $this->addGet(
            '/:module/' . $this->uri . '/:controller/:params',
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
                '/:module/' . $this->uri . '/:controller/:action/:params',
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
