<?php

namespace PhpTwinfield\Services;

use PhpTwinfield\Request\Query\Bank;
use PhpTwinfield\Request\Query\Query;
use PhpTwinfield\Response\IndividualMappedResponse;
use PhpTwinfield\Response\MappedResponseCollection;
use PhpTwinfield\Response\Response;

class QueryService extends BaseService
{
    public string $headerTag = "Authentication";

    /**
     * Advise is to limit the number of children within a parent to 25. So 25 elements within a <general> element, 25
     * elements within e.g. a <transactions> element and so on.
     *
     * @link https://accounting.twinfield.com/webservices/documentation/#/GettingStarted/FUP
     */
    private const MAX_CHILDREN = 25;

    protected function WSDL(): string
    {
        return "/webservices/BankStatementService.svc?wsdl";
    }

    /**
     * @param array $items
     * @return array[]
     */
    public function chunk(array $items): array
    {
        return array_chunk($items, self::MAX_CHILDREN);
    }

    /**
     * Send a DOMDocument to the Twinfield service.
     *
     * A document can be both an object for storage or a search / retrieval request.
     *
     * If there is an error in the response, an Exception is thrown, but it can also throw SoapFaults.
     *
     * Note that you will probably have to chunk the objects into several documents if you want to send many objects.
     *
     * @param Query query parameters
     * @return \stdClass
     *
     * @see \PhpTwinfield\ApiConnectors\BaseApiConnector::sendXmlDocuments()
     * @throws \SoapFault
     * @throws \ErrorException
     */
    public function sendQuery(Query $query)
    {
        return $this->Query($query->getParameters());
    }

    /**
     * Map an array of Responses to IndividualMappedResponse using a callback, wrapped in MappedResponseCollection.
     *
     * @see IndividualMappedResponse
     * @see MappedResponseCollection
     * @param Response[] $responses
     * @param string $individualTag Tag that contains each sub response (e.g. "transaction")
     * @param callable $mapCallback The callback should return the mapped object (e.g. a PurchaseTransaction) based on the response.
     * @return MappedResponseCollection
     */
    public function mapAll(array $responses, string $individualTag, callable $mapCallback): MappedResponseCollection
    {
        $mappedResponses = new MappedResponseCollection();

        foreach ($responses as $response) {
            $document = $response->getResponseDocument();

            /** @var \DOMElement[]|\DOMNodeList $nodeList */
            $nodeList = $document->getElementsByTagName($individualTag);

            foreach ($nodeList as $element) {

                $xml = $document->saveXML($element);
                $subResponse = Response::fromString($xml);

                $mappedResponses->append(new IndividualMappedResponse($subResponse, $mapCallback));
            }
        }

        return $mappedResponses;
    }
}
