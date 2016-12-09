<?php

namespace phpunionplatform\Library\upcreader;

class UpcReader
{
    public function __construct()
    {
    }

    /**
     * @param $serverUpc
     * @return array
     */
    public function readUpc($serverUpc)
    {
        // wrap around a 'root' for easy reading through simplexml
        $xml = simplexml_load_string(trim('<root>'.$serverUpc.'</root>'));

        $upc = array(
            'responseNumber' => (int) preg_replace("/[^0-9]/", "", $xml->U->M),
            'responseValues' => array()
        );

        if (!empty($xml->U->L->A)) {
            foreach ($xml->U->L->A as $argument) {
                $upc['responseValues'][] = (string)$argument;
            }
        }

        return $upc;
    }
}