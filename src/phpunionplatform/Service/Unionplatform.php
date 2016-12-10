<?php

namespace phpunionplatform\Service;

use phpunionplatform\Exception\PhpunionplatformException;
use phpunionplatform\Library\Client\HttpClientInterface;
use phpunionplatform\Library\Querybuilder\HttpQueryBuilder;
use phpunionplatform\Library\Enum\UpcHttpRequestMode;
use phpunionplatform\Library\Enum\UpcHttpRequestParam;
use phpunionplatform\Library\Enum\UpcMessageId;
use phpunionplatform\Library\Upcbuilder\UpcBuilder;
use phpunionplatform\Library\Upcreader\UpcReader;

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
       // $this->upcBuilder = new UpcBuilder();
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
        $userAgent = ''
            . 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0)'
            . 'Gecko/20100101 Firefox/47.0;2.1.1 (Build 856)';

        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CLIENT_HELLO);

        $upc->addArgument('Orbiter');
        $upc->addArgument($userAgent);
        $upc->addArgument('1.10.3');

        // create a postfield query string
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND_RECEIVE,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA => utf8_encode($upc->getUpc())
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
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_CREATE_ROOM);
        $upc->addArgument($roomId);
        return $this->sendUpc($upc);
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
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_JOIN_ROOM);
        $upc->addArgument($roomId);
        return $this->sendUpc($upc);
    }

    /**
     * Send a message to a specific
     * user in a room
     *
     * @todo fix the includeSelf param, this causes an exception on the server
     *
     * @param string $roomId
     * @param string $message
     * @param bool $includeSelf
     * @param array $filters
     * @param array $params
     * @return array
     * @internal param int $userId
     */
    public function sendMessage(
        $roomId,
        $message,
        $includeSelf = false,
        array $filters = array(),
        array $params = array()
    ) {
        $upc = new UpcBuilder(UpcMessageId::MESSAGE_ID_SEND_MESSAGE_TO_ROOMS);

        $upc->addArgument($message);
        $upc->addArgument($roomId);
        $upc->addArgument(($includeSelf) ? 'true' : 'false');
        $upc->addFilters($filters);

        if (count($params) > 0) {
            foreach ($params as $param) {
                $upc->addArgument($param);
            }
        }

        return $this->sendUpc($upc);
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

    private function sendUpc(UpcBuilder $upc)
    {
        $data = $this->querybuilder->buildHttpQuery(
            UpcHttpRequestMode::HTTP_REQUEST_MODE_SEND,
            array(
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_REQUEST_ID   => $this->getRequestNumber(),
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_SESSION_ID   => $this->sessionId,
                UpcHttpRequestParam::HTTP_REQUEST_PARAM_DATA         => $upc->getUpc()
            )
        );

        return $this->upcReader->readUpc(
            $this->httpClient->send($data)
        );
    }
}
