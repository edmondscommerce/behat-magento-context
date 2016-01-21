<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;
use InvalidArgumentException;

class ProductContext extends AbstractMagentoContext
{
    /**
     * @var string
     */
    private $simpleUri = 'default';
    /**
     * @var string
     */
    private $bundleUri = 'default';
    /**
     * @var string
     */
    private $configurableUri = 'default';
    /**
     * @var string
     */
    private $categoryUri = 'default';

    /**
     * ProductContext constructor.
     * @param string $categoryUri
     * @param string $simpleUri
     * @param string $bundleUri
     * @param string $configurableUri
     */
    public function __construct($categoryUri = null, $simpleUri = null, $bundleUri = null, $configurableUri = null)
    {
        $this->simpleUri = $simpleUri;
        $this->bundleUri = $bundleUri;
        $this->configurableUri = $configurableUri;
        $this->categoryUri = $categoryUri;
    }

    /**
     * @Given /^I am on a configurable product page$/
     */
    public function iAmOnAConfigurableProductPage()
    {
        $this->visitPath('/'.$this->configurableUri);
    }

    /**
     * @Given I am on a simple product page
     */
    public function iAmOnASimpleProductPage()
    {
        $this->visitPath('/'.$this->simpleUri);
    }

    /**
     * @Given I am on a bundle product page
     */
    public function iAmOnABundleProductPage()
    {
        $this->visitPath('/'.$this->bundleUri);
    }

    /**
     * @Given I am on a category page
     */
    public function iAmOnACategoryPage()
    {
        $this->visitPath('/'.$this->categoryUri);
    }


    /**
     * @param $productId
     * @Then /^I add a product with a productId "([^"]*)" to the cart$/
     */
    public function iAddProductsToTheCart($productId)
    {
        $this->iAmOnAConfigurableProductPage();
        $this->_jsEvents->iWaitForDocumentReady();
        $this->_cart->iClickTheAddToCartButton("#cart_button_$productId button");
        $this->_cart->iCloseTheCartPopup();
    }

    /**
     * @Then I add the bundle product to the cart
     */
    public function iAddTheBundleProductToTheCart()
    {
        $this->_html->iClickOnTheElement('.bundle-add-to-basket');
        $this->_jsEvents->iWaitForAjaxToFinish();
        $this->_cart->iCloseTheCartPopup();
    }

    /**
     * Choose an option for a configurable product
     *
     * @Then /^I choose product option "([^"]*)"$/
     */
    public function iChooseProductOptionFor($option)
    {
        //Get the container
        $session = $this->getSession();
        $optionContainer = $session->getPage()->findById('product-options-wrapper');

        /** @var NodeElement[] $values */
        $values = $optionContainer->findAll('xpath', 'dl/dd/div/ul/li/a');

        foreach ($values as $v)
        {
            if ($v->getAttribute('name') == $option)
            {
                //Chose the option
                $v->click();
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find a product option: "%s"', $option));
    }

    /**
     * Add the product on the product page to cart, just clicks the add to cart element
     * @Then /^I add to cart$/
     */
    public function iAddToCart()
    {
        $this->_html->iClickOnTheFirstVisibleText('Add to Cart');
        $this->_jsEvents->iWaitForDocumentReady();
    }
}