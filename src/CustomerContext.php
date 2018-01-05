<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
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
        $customer       = $this->_customer;
        $customerQuotes = Mage::getModel('sales/quote')->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());
        foreach ($customerQuotes as $quote)
        {
            $quote->delete();
        }
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
        if (is_null($this->_customer))
        {
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
        $el = $this->getSession()->getPage()->find('named', array('content', 'My Account'));

        $el->click();
    }

    /**
     * @When I click on the Log In link
     */
    public function iClickOnTheLogInLink()
    {
        $el = $this->getSession()->getPage()->find('named', array('content', 'Log In'));

        $el->click();
    }

    /**
     * @Then I log in with email address :username and password :password
     *
     * This is used to test the login method
     *
     * @param $email - The email to log in with
     * @param $password - The password to log in with
     *
     * @throws Exception
     *
     */
    public function iLogIn($email, $password)
    {
        $this->_jsEvents->iWaitForDocumentReady();

        //Override the default login button click if config set
        $loginXpath = self::getMagentoConfigValue('loginXpath');
        if ($loginXpath !== null)
        {
            $nodes = $this->getSession()->getPage()->findAll('xpath', $loginXpath);
            if (empty($nodes))
            {
                throw new ExpectationException('Could not find log in button with Xpath: ' . $loginXpath, $this->getSession()->getDriver());
            }
            $clicked = false;
            foreach ($nodes as $node)
            {
                if ($node && $node->isVisible())
                {
                    $node->click();
                    $clicked = true;
                    break;
                }
            }
            if (!$clicked)
            {
                throw new ExpectationException('Could not find a visible log in button with Xpath: ' . $loginXpath, $this->getSession()->getDriver());
            }
        }

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
     * @throws Exception
     */
    public function iAmLoggedIn()
    {
        if (is_null($this->_customerEmail) || is_null($this->_customerPassword))
        {
            throw new \Exception('You must create the customer before using this step');
        }
        $this->visitPath('/');
        $this->iLogIn($this->_customerEmail, $this->_customerPassword);
        $text = $this->getSession()->getPage()->getText();

        $loginCheckXpath = self::getMagentoConfigValue('loginCheckXpath');
        if (null === $loginCheckXpath)
        {
            //Use the default behaviour, look for the login text
            if (false !== strpos($text, 'Hello, Behat Customer!'))
            {
                return;
            }
        } else
        {
            //Search using the Xpath, if a node is returned then we are logged in
            $this->_html->findOneOrFail('xpath', $loginCheckXpath, 'Unable to login, login check failed');
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


    /**
     * @Then /^The Invoice totals should be as follows$/
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function theCustomerFrontendInvoiceTotalsShouldBeAsFollows(TableNode $comparisonTable)
    {
        $this->getSession()->visit('https://www.staging.ipm.desktop.com/sales/order/invoice/order_id/602/');
        //Need to extract the totals, find the invoice table
        $comparisonTable = $comparisonTable->getRowsHash();
        $xpath           = '//table[contains(@id, "my-invoice-table")]/tfoot';

        $invoiceTables = array_map(function (NodeElement $element)
        {
            return $this->_html->getTable($element);
        }, $this->_html->findAllOrFail('xpath', $xpath));


        //Map the values
        $totals = [];
        foreach ($invoiceTables as $tableData)
        {
            $tableDataTotal = [];
            foreach ($tableData as $datum)
            {
                $tableDataTotal[$datum[0]] = $datum[1];
            }
            $totals[] = $tableDataTotal;
        }

        foreach ($totals as $totalTable)
        {
            try
            {
                $this->compareTableToInvoiceTable($comparisonTable, $totalTable);
            } catch (ExpectationException $e)
            {
                //One row didn't get a match, skip the table
                continue;
            }
        }

        if ($e instanceof Exception)
        {
            throw $e;
        }
    }

    /**
     * Compares two table arrays
     * @param array $comparisonTable
     * @param $invoiceTableTotals
     * @throws ExpectationException
     */
    public function compareTableToInvoiceTable(array $comparisonTable, $invoiceTableTotal)
    {
        //Compare with the given table
        foreach ($comparisonTable as $key => $item)
        {
            if (!isset($invoiceTableTotal[$key]))
            {
                throw new ExpectationException('Could not find total ' . $key, $this->getSession()->getDriver());
            }

            if ($invoiceTableTotal[$key] !== $item)
            {
                throw new ExpectationException('Total for ' . $key . ' (' . $invoiceTableTotal[$key] . ') did not match ' . $item, $this->getSession()->getDriver());
            }
        }
    }
}