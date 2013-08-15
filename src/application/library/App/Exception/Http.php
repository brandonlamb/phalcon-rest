<?php

namespace App\Exception;

class Http extends \Exception
{
    public $devMessage;
    public $errorCode;
    public $response;
    public $additionalInfo;

    /**
     * An HTTP exception that knows how to respond
     * @param string $message
     * @param int $code
     * @param array $errors
     */
    public function __construct($message, $code = 200, array $errors = array())
    {
        isset($errors['dev']) && $this->devMessage = $errors['dev'];
        isset($errors['internalCode']) && $this->errorCode = $errors['internalCode'];
        isset($errors['more']) && $this->additionalInfo = $errors['more'];

        $this->message = $message;
        $this->code = $code;
        $this->response = $this->getResponseDescription($code);
    }

    public function send()
    {
        $di = \Phalcon\DI::getDefault();
        $res = $di->get('response');
        $req = $di->get('request');

        // query string, filter, default
        if (null === $req->get('suppress_response_codes', null, null)) {
            $res->setStatusCode($this->getCode(), $this->response)->sendHeaders();
        } else {
            $res->setStatusCode(200, 'OK')->sendHeaders();
        }

        $error = array(
            'errorCode' => $this->getCode(),
            'userMessage' => $this->getMessage(),
            'devMessage' => $this->devMessage,
            'more' => $this->additionalInfo,
            'applicationCode' => $this->errorCode,
        );

        if (!$req->get('type') || $req->get('type') == 'json') {
            $response = new \App\Response\Json();
            return $response->send($error, true);
        } elseif ($req->get('type') == 'csv') {
            $response = new \App\Response\Csv();
            return $response->send(array($error));
        }

        error_log('HttpException: ' . $this->getFile() . ' at ' . $this->getLine());
    }

    protected function getResponseDescription($code)
    {
        $codes = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',

            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',

            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );

        return (isset($codes[$code])) ? $codes[$code] : 'Unknown Status Code';
    }
}
