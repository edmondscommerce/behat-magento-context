<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Mage;

class TaxFixture extends AbstractMagentoContext
{

    public function getProductTaxClass($className = 'Taxable Goods')
    {
        $productTaxClass = Mage::getModel('tax/class')
                               ->getCollection()
                               ->addFieldToFilter('class_name', $className)
                               ->load()
                               ->getFirstItem();

        if (is_null($productTaxClass->getId())) {
            throw new \Exception('No Tax class with that name');
        }

        return $productTaxClass;
    }

    public function getCustomerTaxClass($className = 'Retail Customer')
    {

        $customerTaxClass = Mage::getModel('tax/class')
                                ->getCollection()
                                ->addFieldToFilter('class_name', $className)
                                ->load()
                                ->getFirstItem();

        if (is_null($customerTaxClass->getId())) {
            throw new \Exception('No Customer Tax class with that name');
        }

        return $customerTaxClass;
    }

    public function createCalculationRate(
        $country,
        $rate,
        $identifier = 'Behat Tax Rate',
        $region = 0,
        $postCode = '*',
        $postCodeIsRange = 0
    ) {
        /** @var \Mage_Tax_Model_Calculation_Rate $taxCalculationRate */
        $taxCalculationRate = Mage::getModel('tax/calculation_rate')->loadByCode($identifier);
        $rateId = null;
        if(!is_null($taxCalculationRate->getId())) {
            $rateId = $taxCalculationRate->getId();
        }
        $taxCalculationRate->setData(array(
                                         "code"           => $identifier,
                                         "tax_country_id" => $country,
                                         "tax_region_id"  => $region,
                                         "zip_is_range"   => $postCodeIsRange,
                                         "tax_postcode"   => $postCode,
                                         "rate"           => $rate,
                                         'tax_calculation_rate_id' => $rateId
                                     ));
        $taxCalculationRate->save();

        return $taxCalculationRate;
    }

    public function createTaxRule($customerTaxClass, $productTaxClass, $taxCalculationRate, $code = 'Behat Tax Rule')
    {
        $ruleModel = Mage::getModel('tax/calculation_rule')->getCollection()->addFieldToFilter('code', $code)->load()
                         ->getFirstItem();
        $ruleModelId = $ruleModel->getId();
        $freePort = Mage::getModel('tax/calculation_rate')->loadByCode('Freeport');
        $ruleModel->setData(array(
                    "code"               => $code,
                    "tax_customer_class" => array($customerTaxClass->getId()),
                    "tax_product_class"  => array($productTaxClass->getId()),
                    "tax_rate"           => array($taxCalculationRate->getId(), $freePort->getId()),
                    "priority"           => "0",
                    "position"           => "0",
                ));
        if(!is_null($ruleModelId)) {
            $ruleModel->setId($ruleModelId);
        }

        $ruleModel->save();
    }

}