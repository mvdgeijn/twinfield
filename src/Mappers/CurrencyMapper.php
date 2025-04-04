<?php
namespace PhpTwinfield\Mappers;

use PhpTwinfield\Currency;
use PhpTwinfield\CurrencyLine;
use PhpTwinfield\Response\Response;

/**
 * Maps a response DOMDocument to the corresponding entity.
 *
 * @package PhpTwinfield
 * @subpackage Mapper
 * @author Willem van de Sande <W.vandeSande@MailCoupon.nl>
 */
class CurrencyMapper extends BaseMapper
{

    /**
     * Maps a Response object to a clean Currency entity.
     *
     * @access public
     *
     * @param \PhpTwinfield\Response\Response $response
     *
     * @return Currency
     * @throws \PhpTwinfield\Exception
     */
    public static function map(Response $response)
    {
        // Generate new Article object
        $currency = new Currency();

        // Gets the raw DOMDocument response.
        $responseDOM = $response->getResponseDocument();

        // Article elements and their methods
        $articleTags = [
            'code'                       => 'setCode',
            'office'                     => 'setOffice',
            'name'                       => 'setName',
            'shortname'                  => 'setShortName'
        ];

        $ratesDOMTag = $responseDOM->getElementsByTagName('rates');

        if (isset($ratesDOMTag) && $ratesDOMTag->length > 0) {
            // Element tags and their methods for lines
            $lineTags = [
                'status'    => 'setStatus',
                'startdate' => 'startdate',
                'rate'      => 'setRate'
            ];

            $ratesDOM = $ratesDOMTag->item(0);

            // Loop through each returned line for the article
            foreach ($ratesDOM->getElementsByTagName('rate') as $rateDom) {

                // Make a new tempory ArticleLine class
                $rateLine = new currencyLine();

                // Set the attributes ( id,status,inuse)
                $rateLine->setStatus($rateDom->getAttribute('status'))
                    ->setStartdate($rateDom->getAttribute('startdate'))
                    ->setRate($rateDom->getAttribute('rate'));

                // Add the bank to the customer
                $currency->addLine($rateLine);

                // Clean that memory!
                unset ($rateLine);
            }
        }
        return $currency;
    }
}
