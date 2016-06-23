<?php

namespace phpunionplatform\library\UpcBuilder;

class UpcBuilder
{
    public function __construct() { }

    public function read($upc)
    {
        $xml = simplexml_load_string(trim('<root>'.$upc.'</root>'));
        return $xml;
    }
}