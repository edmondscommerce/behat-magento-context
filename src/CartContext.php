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
        $this->_pageNavigation->iClickOnTheElement('.skip-cart');
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