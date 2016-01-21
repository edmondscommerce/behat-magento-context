<?php namespace EdmondsCommerce\BehatMagentoOneContext;

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
}