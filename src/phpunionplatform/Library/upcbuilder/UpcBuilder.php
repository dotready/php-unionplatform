<?php

namespace phpunionplatform\Library\upcbuilder;

class UpcBuilder
{
    public function __construct()
    {
    }

    /**
     * @param $upcCode
     * @param array $arguments
     * @return mixed
     */
    public function buildUpc($upcCode, array $arguments)
    {
        $xml = new \SimpleXMLElement('<root/>');

        $unionDoc = $xml->addChild('U');

        // add the upc code
        $unionDoc->addChild('M', 'u' . $upcCode);

        // create the argument list
        $list = $unionDoc->addChild('L');

        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                if (gettype($argument) === 'object') {
                    $la = $list->addChild('A');
                    $this->sxmlAppend($la, $argument);
                } else {
                    $list->addChild('A', $argument);
                }
            }
        }

        return $unionDoc->asXML();
    }

    private function sxmlAppend(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
