<?php

namespace phpunionplatform\library\UpcBuilder;

class UpcBuilder
{
    public function __construct() { }

    /**
     * @param $upcCode
     * @param array $arguments
     * @return mixed
     */
    public function build($upcCode, array $arguments)
    {
        $xml = new \SimpleXMLElement('<U/>');

        // add the upc code
        $xml->addChild('M', 'u' . $upcCode);

        // create the argument list
        $list = $xml->addChild('L');

        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                $list->addChild('A', $argument);
            }
        }

        return $xml->asXML();
    }
}
