<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Mage;

class AdminContext extends AdminFixture
{
    protected $_amLoggedIn = false;

    /** @BeforeScenario */
    public function before($event)
    {
        $this->_amLoggedIn = false;
    }


    /**
     * @Given /^There is an admin user with a username of (.*) and password of (.*)$/
     */
    public function thereIsAnAdminUser($userName, $password)
    {
        $this->createAdminUser($userName, $password);
    }

    /**
     * @Given /^There is an admin login with username (.*) and password (.*)$/
     */
    public function setAdminLoginDetails($userName, $password)
    {
        $this->_userName = $userName;
        $this->_password = $password;
    }

    /**
     * @Given I log into the admin
     */
    public function iLogInToTheAdmin()
    {
        if (is_null($this->_userName) || is_null($this->_password)) {
            throw new \Exception('You must create the admin user first');
        }

        $adminUrl = $this->getAdminUrl();
        $this->visitPath($adminUrl);
        $this->_mink->fillField('username', $this->_userName);
        $this->_mink->fillField('login', $this->_password);
        $this->_html->iClickOnTheElement('.form-button');
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Dashboard');
        Mage::getConfig()->saveConfig('admin/security/use_form_key', 0);
        $this->_amLoggedIn = true;
    }

    /**
     * @When I go to the orders page
     */
    public function iGoToTheOrdersPage()
    {
        if ($this->_amLoggedIn === false) {
            $this->iLogInToTheAdmin();
        }
        $ordersUrl = $this->getAdminUrl('adminhtml/sales_order/index');
        $this->visitPath($ordersUrl);
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Orders');
    }

    /**
     * @Given I log into the admin using url
     * @throws \Exception
     */
    public function iLogInToTheAdminUrl()
    {
        if ($this->_userName === null || $this->_password === null) {
            throw new \RuntimeException('You must create the admin user first');
        }

        $adminUrl = self::$_magentoSetting['AdminUrl'];
        $this->visitPath($adminUrl);
        $this->_mink->fillField('username', $this->_userName);
        $this->_mink->fillField('login', $this->_password);
        $this->_html->iClickOnTheElement('.form-button');
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Dashboard');
        $this->_amLoggedIn = true;
    }

    /**
     * @When I go to the orders page using url
     * @throws \Exception
     */
    public function iGoToTheOrdersUrlPage()
    {
        if ($this->_amLoggedIn === false) {
            $this->iLogInToTheAdmin();
        }
        $ordersUrl = self::$_magentoSetting['AdminUrl'] . '/sales_order/index';
        $this->visitPath($ordersUrl);
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Orders');
    }

    /**
     * @When Select the most recent order
     */
    public function selectTheMostRecentOrder()
    {
        $orderGrid     = $this->getSession()->getPage()->findById('sales_order_grid_table');
        $firstViewLink = $orderGrid->find('css', 'tr td.last a');
        $firstViewLink->click();
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Order View');
    }

    /**
     * @Then I should see :expectedNumber product ordered with the following details
     */
    public function iShouldSeeProductOrderedWithTheFollowingDetails($expectedNumber, TableNode $table)
    {
        $productData      = $this->_getOrderProductTableData();
        $numberOfProducts = count($productData);
        if ($numberOfProducts != (int)$expectedNumber) {
            throw new \Exception("Expected $expectedNumber of products found $numberOfProducts");
        }
        $data    = $table->getTable();
        $headers = [];
        foreach ($data as $row) {
            if (count($headers) == 0) {
                $headers = $row;
                continue;
            }
            $combined = array_combine($headers, $row);
            foreach ($combined as $key => $value) {
                $found = false;
                foreach ($productData as $product) {
                    if (!isset($product[$key])) {
                        continue;
                    }
                    if ($product[$key] == $value) {
                        $found = true;
                    }
                    if ($key == 'Product' && strpos($product[$key], $value) !== false) {
                        $found = true;
                    }
                }
                if ($found !== true) {
                    throw new \Exception("Expected column $key to have a value of $value");
                }
            }


        }

    }

    /**
     * @Then The order totals should be as follows
     */
    public function theOrderTotalsShouldBeAsFollows(TableNode $table)
    {
        $orderTotals = $this->_getOrderTotals();
        $data        = $table->getTable();
        foreach ($data as $expectedTotals) {
            if (count($expectedTotals) !== 2) {
                throw new \Exception('Malformed table. Each row should have two columns');
            }
            $expectedValue = array_pop($expectedTotals);
            $expectedKey   = array_pop($expectedTotals);
            if (!isset($orderTotals[$expectedKey])) {
                throw new \Exception("Expected to find a total of $expectedKey but this was not found on the page");
            }
            if ($orderTotals[$expectedKey] !== $expectedValue) {
                throw new \Exception("Expected $expectedKey to have a value of $expectedValue, found $orderTotals[$expectedKey]");
            }
        }
    }

    protected function _getOrderProductTableData()
    {
        $table   = $this->getSession()->getPage()->find('css', 'table.data.order-tables');
        $rows    = [];
        $headers = [];
        if (is_null($table)) {
            throw new \Exception('Could not find the product grid');
        }
        foreach ($table->findAll('xpath', '/*/tr') as $row) {
#            $rowType = (count($headers) === 0) ? 'th' : 'td';
            $cells = $row->findAll('xpath', '/*');
            $data  = [];
            foreach ($cells as $cell) {
                if (!is_null($cell->find('css', 'input'))) {
                    $label = $cell->find('css', 'input')->getValue();
                } else {
                    $label = $label = $cell->getText();
                }
                $data[] = $label;
            }
            if (count($headers) === 0) {
                $headers = $data;
                continue;
            }
            $rows[] = array_combine($headers, $data);

        }

        return $rows;
    }

    protected function _getOrderTotals()
    {
        $table       = $this->getSession()->getPage()->find('css', '.order-totals table');
        $orderTotals = [];
        if (is_null($table)) {
            throw new \Exception('Colud not find the order totals table');
        }
        $rows = $table->findAll('css', 'tr');
        foreach ($rows as $row) {
            $data = $row->findAll('css', 'td');
            if (count($data) !== 2) {
                throw new \Exception('More than two columns per row');
            }
            $value             = array_pop($data)->getText();
            $key               = array_pop($data)->getText();
            $orderTotals[$key] = $value;
        }

        return $orderTotals;
    }


    /**
     * @Given I am on the customer edit page
     */
    public function iAmOnTheCustomerEditPage()
    {
        $customer   = $this->_customer->getCustomer();
        $customerId = $customer->getId();
        if ($this->_amLoggedIn === false) {
            $this->iLogInToTheAdmin();
        }
        $customerUrl = $this->getAdminUrl('adminhtml/customer/edit', ['id' => $customerId]);
        $this->visitPath($customerUrl);
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_mink->assertPageContainsText('Behat Customer');

    }

    /**
     * @Given /^I click the first order$/
     */
    public function iClickTheFirstOrder()
    {
        $firstOrder = "#sales_order_grid_table > tbody > tr:nth-child(1)";

        $this->getSession()->getPage()->find('css', $firstOrder)->click();
    }


    /**
     * @Given /^I disable form key validation$/
     */
    public function iDisableFormKeyValidation()
    {
        $path = 'admin/security/use_form_key';
        Mage::app()->getStore(0)->setConfig($path, '0');
    }

    /**
     * @Given /^I enable form key validation$/
     */
    public function iEnableFormKeyValidation()
    {
        $path = 'admin/security/use_form_key';
        Mage::app()->getStore(0)->setConfig($path, '1');
    }

    /**
     * @Given /^I am on admin page ([^ ]+)$/
     */
    public function iAmOnAdminPage($path)
    {
        $params = ['_type' => \Mage_Core_Model_Store::URL_TYPE_WEB];

        $adminUrl = Mage::helper('adminhtml')->getUrl($path, $params);

        if (is_null($adminUrl)) {
            throw new Exception('Credit balance page not found.');
        }

        // URL keys were stopping admin pages loading.
        $this->iDisableFormKeyValidation();
        $this->getSession()->visit($adminUrl);
        $this->iEnableFormKeyValidation();
    }

}