<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Exception;
use Mage;

class CartContext extends AbstractMagentoContext implements Context, SnippetAcceptingContext
{

    /**
     * @Given I have my cart open
     * @Then I open the minicart
     * @throws Exception
     */
    public function iOpenTheMiniCart()
    {
        $this->_html->iClickOnTheElement('.skip-cart');
    }

    
    /**
     * @When /^I click on the empty cart link$/
     */
    public function iClickOnTheEmptyCartLink()
    {
        $el = $this->getSession()->getPage()->find('css', 'button[value="empty_cart"]');
        $el->click();
    }


    /**
     * @Given I add a simple products to the cart
     * @Given /^I add ([\d]+) simple products to the cart$/
     * @param int $arg1
     */
    public function iAddASimpleProductsToTheCart($arg1 = 1)
    {
        $this->_product->iAmOnASimpleProductPage();
        $this->iSetTheQuantityTo($arg1);
        $this->_product->iAddToCart();
    }


    /**
     * @Given /^I should see a quantity of (\d+) in the cart$/
     */
    public function iShouldSeeAQuantityOfInTheCart($arg1)
    {
        $field = $this->getProductQuantityFromCartPage();

        if (intval($field->getValue()) != intval($arg1))
        {
            throw new Exception('Expected a product quantity of ' . $arg1 . ' but found ' . $field->getValue());
        }
    }

    /**
     * @Given /^I click on the Remove Product link$/
     */
    public function iClickOnTheRemoveProductLink()
    {
        $el = $this->getSession()->getPage()->find('css', '.product-cart-remove a.btn-remove');

        $el->click();
    }

    /**
     * @When /^I set the quantity to (\d+)$/
     */
    public function iSetTheQuantityTo($arg1)
    {
        $el = $this->getSession()->getPage()->find('css', '#qty');
        if(!$el)
        {
            throw new Exception('Could not find the quantity field on the product page');
        }
        $el->setValue($arg1);
    }

    /**
     * @return \Behat\Mink\Element\NodeElement|null
     */
    protected function getProductQuantityFromCartPage()
    {
        return $this->getSession()->getPage()->find('css', '#shopping-cart-table .input-text.qty');
    }

    /**
     * @When /^I click on the Empty Cart link$/
     */
    public function iClickOnTheEmptyCartLink1()
    {
        //$el = $this->getSession()->getPage()-
    }

    /**
     * @Then /^The price in the cart should be (\D+[0-9\.\,]+)$/
     */
    public function thePriceInTheCartShouldBe($arg1)
    {
        throw new PendingException();
    }

    protected function showMiniCartContents()
    {
        $this->getSession()->getPage()->find('css', '.header-minicart a.skip-cart')->click();
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

        //Find the empty cart button
        $xpath = '//*[contains(text(), "Empty Cart") or contains(text(), "Clear")]';
        $search = $this->getSession()->getPage()->find('xpath', $xpath);

        if($search === null)
        {
            throw new ExpectationException('Could not find the clear cart button', $this->getSession()->getPage());
        }

        $search->click();
    }

    /**
     * @Given I am on the cart page
     */
    public function iAmOnTheCartPage()
    {
        $this->getSession()->visit($this->getFrontUrl('checkout/cart/index'));
    }

    /**
     * @Given /^I (?:can|should) see a price of [\D]+([0-9,.]+) in the cart/
     */
    public function iCanSeePriceInCart($arg1)
    {
        
    }
    
}