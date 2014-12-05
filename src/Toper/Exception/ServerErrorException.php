<?php

namespace Toper\Exception;

class ServerErrorException extends RequestException
{
    /**
     * @var \Guzzle\Http\Message\Response $response
     */
    private $response;

    /**
     * @param \Guzzle\Http\Message\Response $response
     * @param int $message
     * @param int $code
     * @param $previous
     */
    public function __construct(\Guzzle\Http\Message\Response $response, $message, $code, $previous)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }
}
