<?php

namespace phpunionplatform\Service;

use phpunionplatform\Exception\PhpunionplatformException;
use phpunionplatform\Library\client\HttpClientInterface;
use phpunionplatform\Library\querybuilder\HttpQueryBuilder;
use phpunionplatform\Library\upcbuilder\UpcBuilder;
use phpunionplatform\Library\upcreader\UpcReader;

class Unionplatform
{
    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var int
     */
    private $requestNumber = 1;

    /**
     * UnionplatformClient constructor.
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->upcBuilder = new UpcBuilder();
        $this->upcReader = new UpcReader();
        $this->httpClient = $client;
        $this->querybuilder = new HttpQueryBuilder();
    }

    /**
     * This is the handshake as defined in the
     * documentation. send a 65 CLIENT HELLO
     *
     * @return string
     * @throws PhpunionplatformException
     */
    public function sayHello()
    {
        // client handshake
        $upc = $this->upcBuilder->buildUpc(
            65,
            array(
                'Orbiter',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0) Gecko/20100101 Firefox/47.0;2.1.1 (Build 856)',
                '1.10.3'
            )
        );

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            'd',
            array(
                'data' => utf8_encode($upc)
            )
        );

        // send through http client
        $srvUpc = $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );

        // if we don't get back the u66 there is a problem
        // and we can not continue
        if ($srvUpc['responseNumber'] !== 66) {
            throw new PhpunionplatformException('Incorrect response number returned');
        }

        // grab the session id, we must send this with every message
        // we send now
        $this->sessionId = $srvUpc['responseValues'][1];

        // we need to poll for more data
        // if there is any, also a good keep-alive method
        $this->poll();

        return $upc;
    }

    /**
     * Create a room
     *
     * @param string $roomId
     * @return array
     * @throws PhpunionplatformException
     */
    public function createRoom($roomId)
    {
        // build the upc request
        $upc = $this->upcBuilder->buildUpc(
            24,
            array(
                $roomId
            )
        );

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            's',
            array(
                'rid' => $this->getRequestNumber(),
                'sid' => $this->sessionId,
                'data' => $upc
            )
        );

        // send through the http client and
        // parse the upc if any
        return $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );
    }

    /**
     * Join a room
     *
     * @param string $roomId
     * @param string $password
     * @return array
     * @throws PhpunionplatformException
     */
    public function joinRoom($roomId, $password = '')
    {
        // client handshake
        $upc = $this->upcBuilder->buildUpc(
            4,
            array(
                $roomId
            )
        );

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            's',
            array(
                'rid' => $this->getRequestNumber(),
                'sid' => $this->sessionId,
                'data' => $upc
            )
        );

        return $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );
    }

    /**
     * Send a message to a specific
     * user in a room
     *
     * @todo fix the includeSelf param, this causes an exception on the server
     *
     * @param string $room
     * @param string $message
     * @param int $userId
     * @param array $params
     * @return array
     * @throws PhpunionplatformException
     */
    public function sendMessage($room, $message, $userId, array $params = array())
    {
        $filter = '';

        if (!empty($userId)) {
            // prepare the filter
            $xml = '<f t="A"><a c="eq"><n><![CDATA[userId]]></n><v><![CDATA[' . $userId . ']]></v></a></f>';
            $filter = simplexml_load_string($xml);
        }

        // set the upc param
        $upcParams = array($message, $room, '0', '0', $filter);

        // add any additional params
        $upcParams = array_merge($upcParams, $params);

        // build upc
        $upc = $this->upcBuilder->buildUpc(1, $upcParams);

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            's',
            array(
                'rid' => $this->getRequestNumber(),
                'sid' => $this->sessionId,
                'data' => $upc
            )
        );

        return $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );
    }

    /**
     * Poll for possible data the server
     * wants to send us
     *
     * @return array
     * @throws PhpunionplatformException
     */
    public function poll()
    {
        if (empty($this->sessionId)) {
            throw new PhpunionplatformException('Session id can not be empty, sayHello() first!');
        }

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            'c',
            array(
                'rid' => $this->getRequestNumber(),
                'sid' => $this->sessionId
            )
        );

        return $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );
    }

    /**
     * Increment sequential for messaging
     * @return int
     */
    private function getRequestNumber()
    {
        $this->requestNumber += 1;
        return $this->requestNumber;
    }
}
