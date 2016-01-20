<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

class CartContext extends AbstractMagentoContext implements Context, SnippetAcceptingContext
{
    /**
     * @Given /^have an empty cart$/
     */
    public function haveAnEmptyCart()
    {

        $this->visitPath('/checkout/cart');
        $text = $this->getSession()->getPage()->getText();
        if (false !== strpos($text, 'CLEAR SHOPPING CART'))
        {
            $this->pressButton('Clear Shopping Cart');
        }
    }
}