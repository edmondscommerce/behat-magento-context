<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Exception;

class CustomerContext extends AbstractMagentoContext
{
    const TEST_USER = 'test@example.com';
    const TEST_PASSWORD = 'password';

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
        $this->iWaitForDocumentReady();
        $this->_pageNavigation->iClickOnTheElement('.skip-account');
        $this->_mink->clickLink('Log In');
        $this->iWaitForDocumentReady();
        $this->_mink->fillField('login[username]', $email);
        $this->_mink->fillField('login[password]', $password);
        $this->_mink->pressButton('Login');
        $this->iWaitForDocumentReady();
    }

    /**
     * This is used to make sure that the customer is logged in. Please make sure that the customer
     * exists before running the test
     *
     * @Given /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->visitPath('/');
        $this->iLogIn(self::TEST_USER, self::TEST_PASSWORD);
        $text = $this->getSession()->getPage()->getText();
        if (false !== strpos($text, 'Hello, Behat Customer!')) {
            return;
        }

        throw new Exception('unable to login');
    }
}