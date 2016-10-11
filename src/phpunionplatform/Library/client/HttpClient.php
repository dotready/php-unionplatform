<?php

namespace phpunionplatform\Library\client;

use phpunionplatform\exception\PhpunionplatformException;

class HttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $port;

    /**
     * HttpClient constructor.
     * @param $host
     * @param $port
     * @param $domain
     */
    public function __construct($host, $port, $domain)
    {
        $this->host = $host;
        $this->port = $port;
        $this->domain = $domain;
    }

    /**
     * @param $data
     * @return mixed
     * @throws PhpunionplatformException
     */
    public function send($data)
    {
        $socket = stream_socket_client("tcp://".$this->host.":" . $this->port, $errno, $errstr, 15);

        if (!$socket) {
            throw new PhpunionplatformException('Socket connection failed');
        }

        $headers = "POST / HTTP/1.1\r\n";
        $headers .= "Host: ".$this->domain.":".$this->port."\r\n";
        $headers .= "Content-Type: application/text/html; charset=utf8;\r\n";
        $headers .= "Content-Length: ".strlen($data)."\r\n";
        $headers .= "Connection: close\r\n";
        $headers .= "\r\n";
        $headers .= $data;
        $headers .= $this->getNullTerminateChar();

        fwrite($socket, $headers);

        $buffer = '';

        while (!feof($socket)) {
            $buffer .= fread($socket, 1024);
        }

        $parts = explode("\r\n\r\n", $buffer);

        return $parts[1];
    }

    /**
     * @return string
     */
    private function getNullTerminateChar()
    {
        return chr(0);
    }
}
