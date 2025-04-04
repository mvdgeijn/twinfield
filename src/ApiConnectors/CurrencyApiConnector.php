<?php

namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Currency;
use PhpTwinfield\DomDocuments\CurrenciesDocument;
use PhpTwinfield\Exception;
use PhpTwinfield\Mappers\CurrencyMapper;
use PhpTwinfield\Office;
use PhpTwinfield\Request as Request;
use PhpTwinfield\Response\MappedResponseCollection;
use PhpTwinfield\Response\Response;
use Webmozart\Assert\Assert;

/**
 * A facade to make interaction with the the Twinfield service easier when trying to retrieve or send information about
 * Currencies.
 *
 * If you require more complex interactions or a heavier amount of control over the requests to/from then look inside
 * the methods or see the advanced guide detailing the required usages.
 *
 * This class is created by copying and changing the ArticleApiConnector class
 * 
 * @author Marc van de Geijn <marc@bhosted.nl>
 */
class CurrencyApiConnector extends BaseApiConnector
{
    /**
     * Requests a specific Article based off the passed in code and optionally the office.
     *
     * @param string $code
     * @param Office $office If no office has been passed it will instead take the default office from the
     *                       passed in config class.
     * @return Currency|bool The requested article or false if it can't be found.
     * @throws Exception
     */
    public function get(string $code, Office $office): Currency
    {
        // Make a request to read a single Currency. Set the required values
        $request_currency = new Request\Read\Currency();
        $request_currency
            ->setOffice($office->getCode())
            ->setCode($code);

        // Send the Request document and set the response to this instance.
        $response = $this->sendXmlDocument($request_currency);

        return CurrencyMapper::map($response);
    }

    /**
     * Sends an Currency instance to Twinfield to update or add.
     *
     * @param Currency $currency
     * @return Currency
     * @throws Exception
     */
    public function send(Currency $currency): Currency
    {
        return $this->unwrapSingleResponse($this->sendAll([$currency]));
    }

    /**
     * @param Currency[] $currencies
     * @return MappedResponseCollection
     * @throws Exception
     */
    public function sendAll(array $currencies): MappedResponseCollection
    {
        Assert::allIsInstanceOf($currencies, Currency::class);

        /** @var Response[] $responses */
        $responses = [];

        foreach ($this->getProcessXmlService()->chunk($currencies) as $chunk) {

            $currenciesDocument = new CurrenciesDocument();

            foreach ($chunk as $currency) {
                $currenciesDocument->addCurrency($currency);
            }

            $responses[] = $this->sendXmlDocument($currenciesDocument);
        }

        return $this->getProcessXmlService()->mapAll($responses, "currency", function(Response $response): Currency {
            return CurrencyMapper::map($response);
        });
    }
}
