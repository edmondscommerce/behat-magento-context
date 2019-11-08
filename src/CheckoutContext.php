<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
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
        $this->_html->iClickOnTheElement('.minicart a.checkout-button');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Then I go to the cart
     */
    public function iGoToTheCart()
    {
        $this->_cart->iOpenTheMiniCart();
        $this->_mink->assertPageContainsText('Edit Item');
        $this->_mink->assertPageNotContainsText('You have no items in your shopping cart.');
        $this->_html->iClickOnTheElement('a.cart-link');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Then /^I should see the css selector "([^"]*)"$/
     * @Then /^I should see the CSS selector "([^"]*)"$/
     */
    public function iShouldSeeTheCssSelector($css_selector) {
        $element = $this->getSession()->getPage()->find("css", $css_selector);
        if (empty($element)) {
            throw new \Exception(sprintf("The page '%s' does not contain the css selector '%s'", $this->getSession()->getCurrentUrl(), $css_selector));
        }
    }


    /**
     * @When I fill in the Billing Address form
     */
    public function iFillInTheBillingAddressForm()
    {
        $billingSelector = self::$_magentoSetting['checkout']['billAddressFormSelector'];

        $billingForm = $this->_html->findOneOrFail('css', $billingSelector);

        $inputs      = $billingForm->findAll('css', 'input,select');
        foreach ($inputs as $input) {
            $inputName = $input->getAttribute('name');

            // Skip non visible elements
            if (!$input->isVisible()) {
                continue;
            }

            switch ($inputName) {
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
                case 'billing[street][]':
                    $value = '123 Main Street';
                    break;
                case 'billing[region_id]':
                    $value = 1;
                    break;
                case 'billing[city]':
                    $value = 'Leeds';
                    break;
                case 'billing[postcode]':
                    $value = 'bd17 7bd';
                    break;
                case 'billing[country_id]':
                    $value = 'GB';
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
        $fieldValues = [
            'shipping[firstname]'  => 'FirstName',
            'shipping[lastname]'   => 'LastName',
            'shipping[telephone]'  => '0123456789',
            'shipping[street][1]'  => 'Street 1',
            'shipping[street][2]'  => 'Street 2',
            'shipping[country_id]' => 'SG',
            'shipping[city]'       => 'City',
            'shipping[postcode]'   => 'AB12 CDE',
            'shipping[region]'     => 'The Shire',
        ];

        $optionalFieldValues = [
            'shipping[company]' => 'The Box Company',
            'shipping[fax]'     => '9876543210',
        ];

        foreach ($fieldValues as $name => $value) {
            $formWrapper->find('css', "input[name='$name'], select[name='$name']")->setValue($value);
        }

        foreach ($optionalFieldValues as $name => $value) {
            $input = $formWrapper->find('css', "input[name='$name'], select[name='$name']");
            if ($input) {
                $input->setValue($value);
            }
        }
    }

    /**
     * TODO: Cross check between onepagecheckout and onestepcheckout
     *
     * @Then The quantity in the cart should be :arg1
     */
    public function theQuantityInTheCartShouldBe($arg1)
    {
        $fieldValue =
            $this->getSession()->getPage()->find('css', '.onestepcheckout-summary td.qty input.qtyinput')->getValue();

        if ($arg1 != $fieldValue) {
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

    /**
     * @Given /^I continue to the next checkout step$/
     * @throws ExpectationException
     */
    public function iContinueToTheNextCheckoutStep()
    {
        $buttons = $this->_html->findAllOrFail('xpath', '//button[contains(., "Continue")]');

        foreach ($buttons as $button) {
            if ($button->isVisible()) {
                $button->press();
                $this->_jsEvents->iWaitForAjaxToFinish();

                return;
            }
        }

        throw new ExpectationException(
            'Could not find continue button for checkout step', $this->getSession()
        );
    }

    /**
     * @Given /^I select the "([^"]*)" shipping method$/
     */
    public function iSelectTheShippingMethod($method)
    {
        $method = $this->_html->findOneOrFail(
            'xpath',
            sprintf('//form[@id="co-shipping-method-form"]//dt[text()="%s"]/following-sibling::dd[1]//input', $method)
        );

        $method->click();
    }

    /**
     * @Given /^I choose the "([^"]*)" payment method$/
     * @throws ExpectationException
     */
    public function iChooseThePaymentMethod($method)
    {
        $xpath = sprintf('//form[@id="co-payment-form"]//dt/label[contains(., "%s")]', $method);

        $method = $this->_html->findOneOrFail('xpath', $xpath);

        try {
            $radioButton = $this->_html->findOrFailFromNode($method, 'xpath', '/..//input');#

            if($radioButton->isVisible()) {
                $radioButton->click();
            }
        } catch (ExpectationException $exception)
        {
            // Could not find the radio button, assuming only this method is available
            return;
        }
    }
}
