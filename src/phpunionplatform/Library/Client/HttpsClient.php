<?php

namespace phpunionplatform\Library\Client;

use phpunionplatform\Exception\PhpunionplatformException;

class HttpsClient implements HttpClientInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $timeout = 5;

    /**
     * HttpsClient constructor.
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
     *
     * @throws PhpunionplatformException
     */
    public function send($data)
    {
        $socket = @stream_socket_client(
            "tcp://" . $this->host . ":" . $this->port,
            $errorNr,
            $errorStr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );

        if (empty($socket)) {
            throw new PhpunionplatformException('Connection timeout');
        }

        stream_set_timeout($socket, $this->timeout);

        stream_set_blocking($socket, true);
        $success = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        stream_set_blocking($socket, false);

        if ($success === false) {
            throw new PhpunionplatformException('SSL negotiation failed');
        }

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
