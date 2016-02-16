<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Mage;
use Mage_Customer_Model_Customer;

class CustomerFixture extends AbstractMagentoContext
{

    protected $_customerEmail;
    protected $_customerPassword;
    /** @var  Mage_Customer_Model_Customer */
    protected $_customer;

    public function createCustomer($email, $password)
    {
        $website   = Mage::getModel('core/website')->load('base');
        $websiteId = $website->getId();
        $customer  = Mage::getModel('customer/customer');
        $customer->setData('website_id', $websiteId)->loadByEmail($email);
        if (is_null($customer->getId())) {
            $stores  = $website->getStoreIds();
            $storeId = array_pop($stores);
            $customer->setData('firstname', 'Behat')
                     ->setData('lastname', 'Customer')
                     ->setData('website_id', $websiteId)
                     ->setData('store_id', $storeId)
                     ->setData('email', $email);
        }
        $customer->setPassword($password);
        $customer->save();
        $this->_customerEmail    = $email;
        $this->_customerPassword = $password;
        $this->_customer         = $customer;
    }

    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            throw new \Exception('You must create the customer');
        }

        return $this->_customer;
    }


}