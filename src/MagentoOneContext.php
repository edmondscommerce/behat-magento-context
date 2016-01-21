<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Exception;

class MagentoOneContext extends AbstractMagentoContext
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
        $this->_pageNavigation->iClickOnTheElement('.minicart a');
        $this->iWaitForDocumentReady();
    }

    /**
     * @Given /^I am on a configurable product page$/
     */
    public function iAmOnAConfigurableProductPage()
    {
        $this->visitPath('/black-5mm-foam-board.html');
    }

    /**
     * @Given I am on a simple product page
     */
    public function iAmOnASimpleProductPage()
    {
        $this->visitPath('paper-film/graph-pads/frisk-graph-pads/frisk-a1-graph-paper-rolls.html');
    }

    /**
     * @Given I am on a bundle product page
     */
    public function iAmOnABundleProductPage()
    {
        $this->visitPath('/ba-hons-graphic-design.html');
    }

    /**
     * @Given I am on a category page
     */
    public function iAmOnACategoryPage()
    {
        $this->visitPath('paper-film/graph-pads/frisk-graph-pads.html');
    }

    /**
     * @param $css
     *
     * @throws Exception
     *
     * @When /^(?:|I )click on the add to cart button identified by "([^"]*)"$/
     */
    public function iClickTheAddToCartButton($css)
    {
        $this->_pageNavigation->iClickOnTheElement($css);
        $this->iWaitForAjaxToFinish();
    }

    /**
     * @Then I close the cart popup
     */
    public function iCloseTheCartPopup()
    {
        $this->_pageNavigation->iClickOnTheElement('.aw-acp-continue');
    }


    /**
     * @param $productId
     * @Then /^I add a product with a productId "([^"]*)" to the cart$/
     */
    public function iAddProductsToTheCart($productId)
    {
        $this->iAmOnAConfigurableProductPage();
        $this->iWaitForDocumentReady();
        $this->iClickTheAddToCartButton("#cart_button_$productId button");
        $this->iCloseTheCartPopup();
    }

    /**
     * @Then I add the bundle product to the cart
     */
    public function iAddTheBundleProductToTheCart()
    {
        $this->_pageNavigation->iClickOnTheElement('.bundle-add-to-basket');
        $this->iWaitForAjaxToFinish();
        $this->iCloseTheCartPopup();
    }

    /**
     * @Then I switch to prices including VAT
     */
    public function iSwitchToPricesIncludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector','Included');
        $this->iWaitForDocumentReady();
    }

    /**
     * @Then I switch to prices excluding VAT
     */
    public function iSwitchToPricesExcludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector','Excluded');
        $this->iWaitForDocumentReady();
    }
}