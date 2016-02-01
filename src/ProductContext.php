<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;
use InvalidArgumentException;

class ProductContext extends ProductFixture
{

    const CONFIGURABLE_URI = 'configurableUri';
    const SIMPLE_URI = 'simpleUri';
    const BUNDLE_URI = 'bundleUri';
    const CATEGORY_URI = 'categoryUri';
    const GROUPED_URI = 'groupedUri';

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
     * Add the product on the product page to cart, just clicks the add to cart element
     * @Then /^I add to cart$/
     */
    public function iAddToCart()
    {
        $this->_html->iClickOnTheFirstVisibleText('Add to Cart');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Given I am on a bundle product page
     */
    public function iAmOnABundleProductPage()
    {
        if (isset(self::$_magentoSetting[self::BUNDLE_URI])) {
            $bundleURI = self::$_magentoSetting[self::BUNDLE_URI];
        } else {
            $bundleURI = 'pillow-and-throw-set.html';
        }
        $this->visitPath('/' . $bundleURI);
    }

    /**
     * @Given I am on a category page
     */
    public function iAmOnACategoryPage()
    {
        if (isset(self::$_magentoSetting[self::CATEGORY_URI])) {
            $categoryURI = self::$_magentoSetting[self::CATEGORY_URI];
        } else {
            $categoryURI = 'women/new-arrivals.html';
        }
        $this->visitPath('/' . $categoryURI);
    }

    /**
     * @Given /^I am on a configurable product page$/
     */
    public function iAmOnAConfigurableProductPage()
    {
        if (isset(self::$_magentoSetting[self::CONFIGURABLE_URI])) {
            $configurableURI = self::$_magentoSetting[self::CONFIGURABLE_URI];
        } else {
            $configurableURI = 'lafayette-convertible-dress.html';
        }
        $this->visitPath('/' . $configurableURI);
    }

    /**
     * @Given /^I am on a grouped product page$/
     */
    public function iAmOnAGroupedProductPage()
    {
        if (isset(self::$_magentoSetting[self::GROUPED_URI])) {
            $groupedURI = self::$_magentoSetting[self::GROUPED_URI];
        } else {
            $groupedURI = 'vase-set.html';
        }
        $this->visitPath('/' . $groupedURI);
    }

    /**
     * @Given I am on a simple product page
     */
    public function iAmOnASimpleProductPage()
    {

        if (isset(self::$_magentoSetting[self::SIMPLE_URI])) {
            $simpleURI = self::$_magentoSetting[self::SIMPLE_URI];
        } else {
            $simpleURI = 'accessories/eyewear/aviator-sunglasses.html';
        }
        $this->visitPath('/' . $simpleURI);
    }

    /**
     * Choose an option for a configurable product
     *
     * @Then /^I choose product option "([^"]*)"$/
     */
    public function iChooseProductOptionFor($option)
    {
        //Get the container
        $session         = $this->getSession();
        $optionContainer = $session->getPage()->findById('product-options-wrapper');

        /** @var NodeElement[] $values */
        $values = $optionContainer->findAll('xpath', 'dl/dd/div/ul/li/a');

        foreach ($values as $v) {
            if ($v->getAttribute('name') == $option) {
                //Chose the option
                $v->click();

                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find a product option: "%s"', $option));
    }
}