<?php

namespace phpunionplatform\Library\Client;

interface HttpClientInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);
}