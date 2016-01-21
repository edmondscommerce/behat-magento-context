<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;
use InvalidArgumentException;

class ProductContext extends AbstractMagentoContext
{
    /**
     * @var string
     */
    private $simpleUri;
    /**
     * @var string
     */
    private $bundleUri;
    /**
     * @var string
     */
    private $configurableUri;

    /**
     * @var string
     */
    private $groupedUri;

    /**
     * @var string
     */
    private $categoryUri;

    /**
     * ProductContext constructor.
     * @param string $categoryUri
     * @param string $simpleUri
     * @param string $bundleUri
     * @param string $groupedUri
     * @param string $configurableUri
     */
    public function __construct($categoryUri = null, $simpleUri = null, $bundleUri = null, $groupedUri = null, $configurableUri = null)
    {
        $this->simpleUri = (empty($simpleUri)) ? 'accessories/eyewear/aviator-sunglasses.html' : $simpleUri;
        $this->bundleUri = (empty($bundleUri)) ? 'pillow-and-throw-set.html' : $bundleUri;
        $this->configurableUri = (empty($configurableUri)) ? 'lafayette-convertible-dress.html' : $configurableUri;
        $this->categoryUri = (empty($categoryUri)) ? 'women/new-arrivals.html' : $categoryUri;
        $this->groupedUri = (empty($groupedUri)) ? 'vase-set.html' : $groupedUri;
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

    /**
     * @Given /^I am on a grouped product page$/
     */
    public function iAmOnAGroupedProductPage()
    {
        $this->visitPath('/'.$this->groupedUri);
    }
}