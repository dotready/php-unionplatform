<?php

namespace phpunionplatform\Library\Upcbuilder;

use phpunionplatform\Library\Enum\UpcFilterNodeAttribute;
use phpunionplatform\Library\Enum\UpcNodeType;

class UpcBuilder
{
    /**
     * @var \DOMDocument
     */
    private $upc;

    /**
     * UpcBuilder constructor.
     * @param string $messageId
     */
    public function __construct(string $messageId)
    {
        $this->upc = new \DOMDocument('1.0', 'utf-8');

        $root = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_UPC);
        $this->upc->appendChild($root);

        $message = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_MESSAGE, $messageId);
        $root->appendChild($message);

        $list = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_LIST);
        $root->appendChild($list);
    }

    /**
     * @param $value
     */
    public function addArgument($value)
    {
        $list = $this->getUpcList();
        $argument = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_ARGUMENT, $value);
        $list->appendChild($argument);
    }

    /**
     * @param array $filters
     */
    public function addFilters(array $filters)
    {
        $list = $this->getUpcList();
        $argument = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_ARGUMENT);
        $list->appendChild($argument);

        $filterNode = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_FILTER);
        $filterNode->setAttribute(
            UpcFilterNodeAttribute::UPC_FILTER_NODE_ATTR_TYPE,
            UpcNodeType::UPC_NODE_TYPE_ARGUMENT
        );

        $argument->appendChild($filterNode);

        if (count($filters) > 0) {
            foreach ($filters as $filter) {
                $this->addFilter($filterNode, $filter['compare'], $filter['name'], $filter['value']);
            }
        }
    }

    /**
     * @param \DOMElement $filterNode
     * @param string $compare
     * @param string $name
     * @param string $value
     */
    public function addFilter(\DOMElement $filterNode, string  $compare, string $name, string $value)
    {
        $filterArgument = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_FILTER_ARGUMENT);
        $filterArgument->setAttribute(UpcFilterNodeAttribute::UPC_FILTER_NODE_ATTR_COMPARE, $compare);
        $filterNode->appendChild($filterArgument);

        $filterName     = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_FILTER_NAME);
        $cDataName      = $this->upc->createCDATASection($name);
        $filterName->appendChild($cDataName);

        $filterValue    = $this->upc->createElement(UpcNodeType::UPC_NODE_TYPE_FILTER_VALUE);
        $cDataValue     = $this->upc->createCDATASection($value);
        $filterValue->appendChild($cDataValue);

        $filterArgument->appendChild($filterName);
        $filterArgument->appendChild($filterValue);
    }

    /**
     * @return string
     */
    public function getUpc() : String
    {
        return $this->upc->saveXML($this->getUpcRoot(), LIBXML_NOXMLDECL);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getUpcList() : \DOMElement
    {
        $nodes = $this->upc->getElementsByTagName(UpcNodeType::UPC_NODE_TYPE_LIST);

        if (!isset($nodes[0])) {
            throw new \Exception('List node not found');
        }

        if (count($nodes) > 1) {
            throw new \Exception('There should be only one list node');
        }

        return $nodes[0];
    }

    private function getUpcRoot() : \DOMElement
    {
        $nodes = $this->upc->getElementsByTagName(UpcNodeType::UPC_NODE_TYPE_UPC);

        if (!isset($nodes[0])) {
            throw new \Exception('Root node not found');
        }

        if (count($nodes) > 1) {
            throw new \Exception('There should be only one root node');
        }

        return $nodes[0];
    }
}
