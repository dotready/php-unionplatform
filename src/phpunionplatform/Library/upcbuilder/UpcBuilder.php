<?php

namespace phpunionplatform\library\upcbuilder;

class UpcBuilder
{
    public function __construct() { }

    /**
     * @param $upcCode
     * @param array $arguments
     * @return mixed
     */
    public function buildUpc($upcCode, array $arguments)
    {
        $xml = new \SimpleXMLElement('<root/>');

        $uniondoc = $xml->addChild('U');

        // add the upc code
        $uniondoc->addChild('M', 'u' . $upcCode);

        // create the argument list
        $list = $uniondoc->addChild('L');

        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                if (gettype($argument) === 'object') {
                    $la = $list->addChild('A');
                    $this->sxml_append($la, $argument);
                } else {
                    $list->addChild('A', $argument);
                }
            }
        }

        return $uniondoc->asXML();

    }

    private function sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
