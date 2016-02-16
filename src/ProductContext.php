<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;
use InvalidArgumentException;
use Mage;

class ProductContext extends ProductFixture
{

    const CONFIGURABLE_URI = 'configurableUri';
    const SIMPLE_URI = 'simpleUri';
    const BUNDLE_URI = 'bundleUri';
    const CATEGORY_URI = 'categoryUri';
    const GROUPED_URI = 'groupedUri';

    protected $_productId;

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

    /**
     * @Then /^There is a (?:|.+ )simple product with an SKU of (.*)$/i
     */
    public function iAmTestingASimpleProductWithAnSkuOfTest($sku)
    {
        $productId = $this->_productId;
        if(is_null($productId)) {
            $productId = 999999;
        }                       else {
            $productId++;
        }
        $this->_productId = $productId;
        $this->createProduct($productId, ['sku' => $sku]);
    }

    /**
     * @Given /^The product has a (.*) of (.*)$/i
     */
    public function theProductHasAnAttributeOf($property, $value)
    {
        if($property == 'price') {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
        }
        $this->setAttribute($property, $value);
    }

    /**
     * @Given The product is taxable
     */
    public function theProductIsTaxable()
    {
       $this->setAttribute('tax_class_id', 2);
    }

    /**
     * @Given The product is not taxable
     */
    public function theProductIsNotTaxable()
    {
        $this->setAttribute('tax_class_id', 0);
    }

    /**
     * @Given I have added :qty of the product to my cart
     */
    public function iHaveAddedOfTheProductToMyCart($qty)
    {
        $url = $this->getProductAttribute('url_path');
        self::$_magentoSetting['simpleUri'] = $url;
        $this->iAmOnASimpleProductPage();
        $this->_mink->fillField('qty', $qty);
        $this->iAddToCart();
    }
}