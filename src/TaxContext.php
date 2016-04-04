<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Mage;

class TaxContext extends TaxFixture
{

    /**
     * @Given /^The tax rate is ([0-9,.]*)% in (.*)$/
     */
    public function theTaxRateIsInSingapore($rate, $country)
    {
        $list = Mage::app()->getLocale()->getCountryTranslationList();
        $list = array_flip($list);
        if (!isset($list[$country])) {
            throw new \Exception("Unknown Country: $country");
        }
        if (!is_numeric($rate)) {
            throw new \Exception('You must provide a numeric tax rate');
        }
        $customerClass = $this->getCustomerTaxClass();
        $productClass  = $this->getProductTaxClass('Behat Tax Rate');
        $rateClass     = $this->createCalculationRate($list[$country], $rate);
        $this->createTaxRule($customerClass, $productClass, $rateClass);
    }
}