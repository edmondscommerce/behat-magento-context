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
        if(count($customer->getAddresses()) < 1 ) {
            $address = Mage::getModel("customer/address");
            $address->setCustomerId($customer->getId())
                    ->setFirstname('Behat')
                    ->setLastname('Customer')
                    ->setCountryId('SG')
                    ->setPostcode('123456')
                    ->setCity('Singapore')
                    ->setTelephone('0123456789')
                    ->setStreet('123 Main Street')
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1');

            $address->save();
        }
        $this->_customerEmail    = $email;
        $this->_customerPassword = $password;
        $this->_customer         = $customer;

        return $customer;
    }

    /**
     * @return Mage_Customer_Model_Customer
     *
     * @throws \Exception
     */
    public function getCustomer()
    {
        if (null === $this->_customer) {
            throw new \Exception('You must create the customer');
        }

        return $this->_customer;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCustomerEmail()
    {
        if (null === $this->_customerEmail) {
            throw new \Exception('You must create the customer');
        }

        return $this->_customerEmail;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCustomerPassword()
    {
        if (null === $this->_customerPassword) {
            throw new \Exception('You must create the customer');
        }

        return $this->_customerPassword;
    }
}