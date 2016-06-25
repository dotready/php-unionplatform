<?php

namespace phpunionplatform\library\client;

interface HttpClientInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);
}