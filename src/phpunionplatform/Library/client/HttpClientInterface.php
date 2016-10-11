<?php

namespace phpunionplatform\Library\client;

interface HttpClientInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);
}