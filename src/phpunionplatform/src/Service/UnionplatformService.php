<?php

namespace phpunionplatform\service;

use phpunionplatform\library\UpcBuilder\UpcBuilder;

class UnionplatformService
{
    private $host;

    private $port;

    private $upcService;

    public function __construct($host, $port, UpcService $upcService)
    {
        $this->host = $host;
        $this->port = $port;
        $this->upcService = $upcService;
    }

    public function sayHello()
    {
        $builder = new UpcBuilder();

        // client handshake
        $upc = $builder->buildUpc('d', 65, array(
                'Orbiter',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:47.0) Gecko/20100101 Firefox/47.0;2.1.1 (Build 856)',
                '1.10.3'
            )
        );

        return $upc;
    }

    private function send($data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->host,
            CURLOPT_PORT => $this->port,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_FORBID_REUSE => true
        ));

        $data = curl_exec($ch);

        return $this->upcService->readUpc($data);
    }
}