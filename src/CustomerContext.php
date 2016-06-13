<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
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
     * @param $attributeName
     * @param $attributeValue
     *
     * @throws Exception
     * 
     * @Given /^The customer has an attribute ([^ ]*) with a value of (.*)$/
     */
    public function theCustomerHasAnAttributeOf($attributeName, $attributeValue)
    {
        if(is_null($this->_customer)) {
            throw new Exception('The customer has not been set');
        }
        $customer = $this->_customer;
        $customer->setData($attributeName, $attributeValue);
        $customer->save();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn())
        {
            throw new Exception('I am not logged in when I should be');
        }
    }

    /**
     * @Given /^There is a user with the following details$/
     */
    public function thereIsAUserWithTheFollowingDetails(TableNode $table)
    {
        $rows = $table->getRows();
        $this->createCustomer($rows[1][0], $rows[1][1]);
    }

    /**
     * @When I click on the My Account link
     */
    public function iClickOnTheAccountLink()
    {
        $el = $this->getSession()->getPage()->find('text', 'My Account');

        $el->click();
    }

    /**
     * @Then I log in with email address :username and password :password
     *
     * This is used to test the login method
     *
     * @param $email    - The email to log in with
     * @param $password - The password to log in with
     *
     * @throws Exception
     *
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

    /**
     * @When /^I fill in the Log In form:$/
     */
    public function iFillInTheLogInForm(TableNode $table)
    {
        $rows = $table->getRows();
        throw new PendingException();
    }

    /**
     * @Given /^I am on the account page$/
     */
    public function iAmOnTheAccountPage()
    {
        $this->getSession()->visit(Mage::getUrl('customer/account/index'));
    }
}