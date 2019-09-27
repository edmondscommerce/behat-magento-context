<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Tester\Exception\PendingException;
use EdmondsCommerce\BehatFakerContext\FakerContext;
use Exception;

class CheckoutContext extends AbstractMagentoContext
{
    /**
     * Simplified version of the following steps, will check that there actually is a product
     *
     * And I open the minicart
     * And I click on the element ".minicart a"
     *
     *
     * @Then I go to the checkout
     */
    public function iGoToTheCheckout()
    {
        $this->_cart->iOpenTheMiniCart();
        $this->_mink->assertPageContainsText('Edit Item');
        $this->_mink->assertPageNotContainsText('You have no items in your shopping cart.');
        $this->_html->iClickOnTheElement('.minicart a');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @When I fill in the Billing Address form
     */
    public function iFillInTheBillingAddressForm()
    {
        $billingSelector = self::$_magentoSetting['checkout']['billAddressFormSelector'];

        $billingForm = $this->getSession()->getPage()->find('css', $billingSelector);
        $inputs      = $billingForm->findAll('css', 'input');
        foreach ($inputs as $input) {
            switch ($input->getAttribute('name')) {
                case 'billing[firstname]':
                    $value = 'Behat';
                    break;
                case 'billing[lastname]':
                    $value = 'Customer';
                    break;
                case 'billing[email]':
                    $value = 'behat@example.com';
                    break;
                case 'billing[telephone]':
                    $value = '0123456789';
                    break;
                case 'billing[street][1]':
                    $value = '123 Main Street';
                    break;
                case 'billing[city]':
                    $value = 'Leeds';
                    break;
                case 'billing[postcode]':
                    $value = 'LS1 2AB';
                    break;
                default:
                    $value = false;
            }
            if ($value === false) {
                continue;
            }
            $input->setValue($value);
        }
    }

    /**
     * @When I fill in the Shipping Address form
     */
    public function iFillInTheShippingAddressForm()
    {
        $formWrapper = $this->getSession()->getPage()->find('css', '#shipping_address_list');
        $fieldValues = array(
            'shipping[firstname]'  => 'FirstName',
            'shipping[lastname]'   => 'LastName',
            'shipping[telephone]'  => '0123456789',
            'shipping[street][1]'  => 'Street 1',
            'shipping[street][2]'  => 'Street 2',
            'shipping[country_id]' => 'SG',
            'shipping[city]'       => 'City',
            'shipping[postcode]'   => 'AB12 CDE',
            'shipping[region]'     => 'The Shire',
        );

        $optionalFieldValues = array(
            'shipping[company]'    => 'The Box Company',
            'shipping[fax]'        => '9876543210'
        );

        foreach ($fieldValues as $name => $value)
        {
            $formWrapper->find('css', "input[name='$name'], select[name='$name']")->setValue($value);
        }

        foreach($optionalFieldValues as $name => $value)
        {
            $input = $formWrapper->find('css', "input[name='$name'], select[name='$name']");
            if($input)
            {
                $input->setValue($value);
            }
        }
    }

    /**
     * TODO: Cross check between onepagecheckout and onestepcheckout
     * @Then The quantity in the cart should be :arg1
     */
    public function theQuantityInTheCartShouldBe($arg1)
    {
        $fieldValue = $this->getSession()->getPage()->find('css', '.onestepcheckout-summary td.qty input.qtyinput')->getValue();

        if ($arg1 != $fieldValue)
        {
            throw new Exception('The expected quantity was ' . $arg1 . ' but found ' . $fieldValue);
        }
    }

    /**
     * @When I decrease the quantity in the cart to :arg1
     */
    public function iDecreaseTheQuantityInTheCartTo($arg1)
    {
        $this->getSession()->getPage()->find('css', '.editcart a.subsqty')->click();
    }


    /**
     * @Then I should see the Order Success Page
     */
    public function iShouldSeeTheOrderSuccessPage()
    {
        $this->getSession()->getPage()->hasContent('Your order has been received');
    }

    /**
     * @Given I am at the checkout
     */
    public function iAmAtTheCheckout()
    {
        throw new PendingException();
    }

}
