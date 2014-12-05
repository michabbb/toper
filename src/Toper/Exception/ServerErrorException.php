<?php

namespace Toper\Exception;

class ServerErrorException extends RequestException
{
    /**
     * @var \Guzzle\Http\Message\Respons $response
     */
    private $response;

    /**
     * @param \Guzzle\Http\Message\Respons $response
     * @param int $message
     * @param int $code
     * @param $previous
     */
    public function __construct(\Guzzle\Http\Message\Respons $response, $message, $code, $previous)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }
}
