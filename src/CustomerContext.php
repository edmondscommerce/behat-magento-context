<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Exception;
use Mage;

class CustomerContext extends CustomerFixture
{

    /**
     * @param $email
     * @param $password
     *
     * @Given /^There is a customer with an email of ([^ ]*) and password of ([^ ]*)$/
     */
    public function thereIsACustomer($email, $password)
    {
        $this->createCustomer($email, $password);
        $customer      = $this->_customer;
        $customerQuote = Mage::getModel('sales/quote')
                             ->setStoreId($customer->getData('store_id'))
                             ->loadByCustomer($customer->getId());
        $customerQuote->delete();
    }

    /**
     * This is used to test the login method
     *
     * @param $email    - The email to log in with
     * @param $password - The password to log in with
     *
     * @throws Exception
     *
     * @Then I log in with email address :username and password :password
     */
    public function iLogIn($email, $password)
    {
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->clickLink('Log In');
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->fillField('login[username]', $email);
        $this->_mink->fillField('login[password]', $password);
        $this->_mink->pressButton('Login');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * This is used to make sure that the customer is logged in. Please make sure that the customer
     * exists before running the test
     *
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        if (is_null($this->_customerEmail) || is_null($this->_customerPassword)) {
            throw new \Exception('You must create the customer before using this step');
        }
        $this->visitPath('/');
        $this->iLogIn($this->_customerEmail, $this->_customerPassword);
        $text = $this->getSession()->getPage()->getText();
        if (false !== strpos($text, 'Hello, Behat Customer!')) {
            return;
        }

        throw new Exception('unable to login');
    }
}