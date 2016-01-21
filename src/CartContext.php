<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Exception;

class CartContext extends AbstractMagentoContext implements Context, SnippetAcceptingContext
{

    /**
     * @throws Exception
     * @Then I open the minicart
     */
    public function iOpenTheMiniCart()
    {
        $this->_navigation->iClickOnTheElement('.skip-cart');
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
        $this->_html->iClickOnTheElement($css);
        $this->_jsEvents->iWaitForAjaxToFinish();
    }

    /**
     * @Then I close the cart popup
     */
    public function iCloseTheCartPopup()
    {
        $this->_html->iClickOnTheElement('.aw-acp-continue');
    }

    /**
     * @Given /^have an empty cart$/
     */
    public function haveAnEmptyCart()
    {
        $this->visitPath('/checkout/cart');
        $text = $this->getSession()->getPage()->getText();
        if (false !== strpos($text, 'CLEAR SHOPPING CART'))
        {
            $this->_mink->pressButton('Clear Shopping Cart');
        }
    }
}