<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use EdmondsCommerce\BehatFakerContext\FakerContext;

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
        $billingForm = $this->getSession()->getPage()->find('css', '#billing_address');
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
}