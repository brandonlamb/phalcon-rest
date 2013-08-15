<?php

namespace App\Response;

class Json extends AbstractResponse
{
    /**
     * @var bool Convert snake case to camel case?
     */
    protected $snake = true;

    /**
     * @var bool Use envelop/meta?
     */
    protected $envelope = true;

    public function send($records, $error = false)
    {
        // Error's come from HTTPException.  This helps set the proper envelope data
        $response = $this->di->getShared('response');
        $success = ($error) ? 'fail' : 'ok';

        // If the query string 'envelope' is set to false, do not use the envelope. Instead, return headers.
        $request = $this->di->get('request');
        if ($request->get('envelope', null, null) === 'false') {
            $this->envelope = false;
        }

        // Most devs prefer camelCase to snake_Case in JSON, but this can be overriden here
        if ($this->snake) {
            $records = $this->arrayKeysToSnake($records);
        }

        $etag = md5(serialize($records));

        if ($this->envelope) {
            // Provide an envelope for JSON responses.  '_meta' and 'records' are the objects.
            $message = array(
                '_meta' => array(
                    'status'    => $success,
                    'count'     => ($error) ? 1 : count($records),
                ),
                'records' => $records,
            );
        } else {
            $response->setHeader('X-Record-Count', count($records));
            $response->setHeader('X-Status', $success);
            $message = $records;
        }

        $response->setContentType('application/json');
        $response->setHeader('E-Tag', $etag);

        // HEAD requests are detected in the parent constructor.
        // HEAD does everything exactly the same as GET, but contains no body.
        if (!$this->head) {
            $response->setJsonContent($message);
        }

        $response->send();

        return $this;
    }

    /**
     * Set flag for whether to convert snake case to camel case
     * @param bool $snake
     * @return self
     */
    public function convertSnakeCase($snake)
    {
        $this->snake = (bool) $snake;
        return $this;
    }

    /**
     * Set flag for whether to include envelope/header in response
     * @param bool $envelope
     * @return self
     */
    public function useEnvelope($envelope)
    {
        $this->envelope = (bool) $envelope;
        return $this;
    }
}
