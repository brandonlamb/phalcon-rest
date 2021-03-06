<?php

namespace App\Controller;

use \Phalcon\DI\Injectable as Injectable,
    \Phalcon\DI as Di,
    \Phalcon\Tag as Tag,
    \App\Exception\Http as HttpException;

/**
 * Base REST controller
 * Supports queries with the following paramters:
 *   Searching: q=(searchField1:value1,searchField2:value2)
 *   Partial Responses: fields=(field1,field2,field3)
 *   Limits: limit=10
 *   Partials: offset=20
 */
class Base extends Injectable
{
    /**
     * If query string contains 'q' parameter.
     * This indicates the request is searching an entity
     * @var boolean
     */
    protected $isSearch = false;

    /**
     * If query contains 'fields' parameter.
     * This indicates the request wants back only certain fields from a record
     * @var boolean
     */
    protected $isPartial = false;

    /**
     * Set when there is a 'limit' query parameter
     * @var integer
     */
    protected $limit;

    /**
     * Set when there is an 'offset' query parameter
     * @var integer
     */
    protected $offset;

    /**
     * Array of fields requested to be searched against
     * @var array
     */
    protected $searchFields;

    /**
     * Array of fields requested to be returned
     * @var array
     */
    protected $partialFields;

    /**
     * Sets which fields may be searched against, and which fields are allowed to be returned in
     * partial responses.  This will be overridden in child Controllers that support searching
     * and partial responses.
     * @var array
     */
    protected $allowedFields = array(
        'search'    => array(),
        'partials'  => array(),
    );

    /**
     * @var array
     */
    protected $data = array();

    /**
     * Called after construct
     */
    public function onConstruct()
    {
        $this->parseRequest($this->allowedFields);
    }

    /**
     * Called before controller->action()
     */
    public function initialize()
    {
#       Tag::prependTitle('Starbucks - ');
#       $this->loadMainTrans();
    }

    /**
     * Define any logic that needs to happen before executing the route
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {

    }

    /**
     * After execution of the route finishes, run this method.
     * Here we figure out which response to create, send the response, then exit.
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function afterExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $request = $this->request;

        // OPTIONS have no body, send the headers, exit
        if ($request->getMethod() == 'OPTIONS') {
            $this->response->setStatusCode('200', 'OK');
            exit($this->response->send());
        }

        // Respond by default as JSON
        if (!$request->get('type') || $request->get('type') == 'json') {
            // Results returned from the route's controller.  All Controllers should return an array
            $records = $this->dispatcher->getReturnedValue();
            $response = new \App\Response\Json($this->di);
            exit($response->useEnvelope(true) //this is default behavior
                ->convertSnakeCase(true) //this is also default behavior
                ->send($records));
        } elseif ($request->get('type') == 'csv') {
            $records = $this->dispatcher->getReturnedValue();
            $response = new \App\Response\Csv();
            exit($response->useHeaderRow(true)->send($records));
        } else {
            throw new HttpException(
                'Could not return results in specified format',
                403,
                array(
                    'dev' => 'Could not understand type specified by type paramter in query string.',
                    'internalCode' => 'NF1000',
                    'more' => 'Type may not be implemented. Choose either "csv" or "json"'
                )
            );
        }
    }

    /**
     * Forward to another controller/action
     * @param string $uri
     */
    protected function forward($uri)
    {
        $parts = explode('/', $uri);
        return $this->dispatcher->forward(
            array('controller' => $parts[0], 'action' => $parts[1])
        );
    }

    /**
     * Main method for parsing a query string.
     * Finds search paramters, partial response fields, limits, and offsets.
     * Sets Controller fields for these variables.
     *
     * @param array $allowedFields Allowed fields array for search and partials
     */
    protected function parseRequest($allowedFields)
    {
        $request = $this->di->get('request');
        $searchParams = $request->get('q', null, null);
        $fields = $request->get('fields', null, null);

        // Set limits and offset, elsewise allow them to have defaults set in the Controller
        $this->limit = ($request->get('limit', null, null)) ?: $this->limit;
        $this->offset = ($request->get('offset', null, null)) ?: $this->offset;

        // If there's a 'q' parameter, parse the fields, then determine that all the fields in the search
        // are allowed to be searched from $allowedFields['search']
        if ($searchParams) {
            $this->isSearch = true;
            $this->searchFields = $this->parseSearchParameters($searchParams);

            // This handly snippet determines if searchFields is a strict subset of allowedFields['search']
            if (array_diff(array_keys($this->searchFields), $this->allowedFields['search'])) {
                throw new HttpException(
                    "The fields you specified cannot be searched.",
                    401,
                    array(
                        'dev' => 'You requested to search fields that are not available to be searched.',
                        'internalCode' => 'S1000',
                        'more' => '' // Could have link to documentation here.
                ));
            }
        }

        // If there's a 'fields' paramter, this is a partial request.
        // Ensures all the requested fields are allowed in partial responses.
        if ($fields) {
            $this->isPartial = true;
            $this->partialFields = $this->parsePartialFields($fields);

            // Determines if fields is a strict subset of allowed fields
            if (array_diff($this->partialFields, $this->allowedFields['partials'])) {
                throw new HttpException(
                    'The fields you asked for cannot be returned.',
                    401,
                    array(
                        'dev' => 'You requested to return fields that are not available to be returned in partial responses.',
                        'internalCode' => 'P1000',
                        'more' => '' // Could have link to documentation here.
                ));
            }
        }
    }

    /**
     * Parses out the search parameters from a request.
     * Unparsed, they will look like this:
     *    (name:Benjamin Framklin,location:Philadelphia)
     * Parsed:
     *     array('name'=>'Benjamin Franklin', 'location'=>'Philadelphia')
     * @param  string $unparsed Unparsed search string
     * @return array An array of fieldname=>value search parameters
     */
    protected function parseSearchParameters($unparsed)
    {
        // Strip parens that come with the request string
        $unparsed = trim($unparsed, '()');

        // Now we have an array of "key:value" strings.
        $splitFields = explode(',', $unparsed);
        $mapped = array();

        // Split the strings at their colon, set left to key, and right to value.
        foreach ($splitFields as $field) {
            $splitField = explode(':', $field);
            $mapped[$splitField[0]] = $splitField[1];
        }

        return $mapped;
    }

    /**
     * Parses out partial fields to return in the response.
     * Unparsed:
     *     (id,name,location)
     * Parsed:
     *     array('id', 'name', 'location')
     * @param  string $unparsed Unparsed string of fields to return in partial response
     * @return array            Array of fields to return in partial response
     */
    protected function parsePartialFields($unparsed)
    {
        return explode(',', trim($unparsed, '()'));
    }

    /**
     * Provides a base CORS policy for routes like '/users' that represent a Resource's base url
     * Origin is allowed from all urls.  Setting it here using the Origin header from the request
     * allows multiple Origins to be served.  It is done this way instead of with a wildcard '*'
     * because wildcard requests are not supported when a request needs credentials.
     *
     * @return true
     */
    public function optionsBase()
    {
        $response = $this->di->get('response');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, HEAD');
        $response->setHeader('Access-Control-Allow-Origin', $this->di->get('request')->header('Origin'));
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Allow-Headers', "origin, x-requested-with, content-type");
        $response->setHeader('Access-Control-Max-Age', '86400');

        return true;
    }

    /**
     * Provides a CORS policy for routes like '/users/123' that represent a specific resource
     *
     * @return true
     */
    public function optionsOne()
    {
        $response = $this->di->get('response');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, PUT, PATCH, DELETE, OPTIONS, HEAD');
        $response->setHeader('Access-Control-Allow-Origin', $this->di->get('request')->header('Origin'));
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Allow-Headers', "origin, x-requested-with, content-type");
        $response->setHeader('Access-Control-Max-Age', '86400');

        return true;
    }

    /**
     * @param array $results
     */
    public function respond($results)
    {
        if ($this->isPartial) {
            $newResults = array();
            $remove = array_diff(array_keys($this->exampleRecords[0]), $this->partialFields);
            foreach ($results as $record) {
                $newResults[] = $this->arrayRemoveKeys($record, $remove);
            }
            $results = $newResults;
        }

        if ($this->offset) {
            $results = array_slice($results, $this->offset);
        }

        if ($this->limit) {
            $results = array_slice($results, 0, $this->limit);
        }

        $this->view->setVar('data', $results);

        return $results;
    }

    private function arrayRemoveKeys($array, $keys = array())
    {
        // If array is empty or not an array at all, don't bother doing anything else.
        if (empty($array) || (!is_array($array))) {
            return $array;
        }

        // At this point if $keys is not an array, we can't do anything with it.
        if (!is_array($keys)) {
            return $array;
        }

        // array_diff_key() expected an associative array.
        $assocKeys = array();
        foreach ($keys as $key) {
            $assocKeys[$key] = true;
        }

        return array_diff_key($array, $assocKeys);
    }

    public function search()
    {
        $results = array();
        foreach ($this->exampleRecords as $record) {
            $match = true;
            foreach ($this->searchFields as $field => $value) {
                if (!(stripos($record[$field], $value) !== false)) {
                    $match = false;
                }
            }
            if ($match) {
                $results[] = $record;
            }
        }

        return $results;
    }

    protected function getTransPath()
    {
        $translationPath = \PATH . '/application/main/messages/';
        $language = $this->session->get('language');
        if (!$language) {
            $this->session->set('language', 'en');
        }
        if ($language === 'es' || $language === 'en') {
            return $translationPath . $language;
        } else {
            return $translationPath . 'en';
        }
    }

    /**
     * Loads a translation for the whole site
     */
    public function loadMainTrans()
    {
        $translationPath = $this->getTransPath();
        require $translationPath . '/main.php';

        //Return a translation object
        $mainTranslate = new \Phalcon\Translate\Adapter\NativeArray(array(
            'content' => $messages
        ));

        //Set $mt as main translation object
        $this->view->setVar('mt', $mainTranslate);
      }

    /**
     * Loads a translation for the active controller
     */
    public function loadCustomTrans($transFile)
    {
        $translationPath = $this->getTransPath();
        require $translationPath . '/' . $transFile . '.php';

        //Return a translation object
        $controllerTranslate = new \Phalcon\Translate\Adapter\NativeArray(array(
            'content' => $messages
        ));

        //Set $t as controller's translation object
        $this->view->setVar('t', $controllerTranslate);
    }

    public function getAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function postAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function putAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function patchAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function deleteAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function headAction()
    {
        throw new HttpException('Method not implemented', 501);
    }

    public function optionsAction()
    {
        throw new HttpException('Method not implemented', 501);
    }
}
