<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Exception;
use InvalidArgumentException;
use Mage;

class ProductContext extends ProductFixture
{

    const CONFIGURABLE_URI = 'configurableUri';
    const SIMPLE_URI = 'simpleUri';
    const BUNDLE_URI = 'bundleUri';
    const CATEGORY_URI = 'categoryUri';
    const GROUPED_URI = 'groupedUri';



    /**
     * @var array Defaults paths for different areas of the catalog
     */
    protected $pathDefaults;

    public function __construct()
    {
        $this->pathDefaults = array(
            self::CONFIGURABLE_URI => '',
            self::SIMPLE_URI       => '',
            self::BUNDLE_URI       => '',
            self::CATEGORY_URI     => '',
            self::GROUPED_URI      => ''
        );
    }

    /**
     * @Given /^I am on the product page$/
     */
    public function iAmOnTheProductPage()
    {
        $url = $this->getProductAttribute('url_path');
        self::$_magentoSetting['simpleUri'] = $url;
        $this->iAmOnASimpleProductPage();
    }

    /**
     * @Given /^I add (\d+) of the product to the cart$/
     */
    public function iAddOfTheProductToTheCart($arg1)
    {
        $this->iAmOnTheProductPage();
        $this->iAddToCart();
    }

    /**
     * Sets a product attribute to the value given
     * @Given /^the product attribute "([^"]*)" is "([^"]*)"$/
     */
    public function theProductAttributeIs($attribute, $value)
    {
        $product = $this->getTheProduct();

        $product->setData($attribute, $value);
        $product->save();
    }

    /**
     * Get the path for a type of catalog page, or returns the default if it isn't set in the config
     * @param $pathName
     * @return mixed
     */
    protected function getPath($pathName)
    {
        if (isset(self::$_magentoSetting[$pathName])) {
            return self::$_magentoSetting[$pathName];
        } else {
            return $this->pathDefaults[$pathName];
        }
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
     * Add the product on the product page to cart, just clicks the add to cart element
     * @Then /^I add to cart$/
     * @When I click the Add To Cart button
     */
    public function iAddToCart()
    {
        $cartText = $this::getMagentoConfigValue('addToCartText', "Add to Cart");
        $this->_html->iClickOnTheFirstVisibleText($cartText);
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Given /^I add a different product to the cart$/
     */
    public function iAddADifferentProductToTheCart()
    {
        //Get a random product that is not in the cart
        $this->iAmOnASimpleProductPage();
        $this->iAddToCart();
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
     * @Then /^I choose product option "[a-zA-Z\$]([^"]*)"$/
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
        $store = Mage::app()->getStore()->getStoreId();
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        Mage::app()->setCurrentStore($store);
        if(is_object($product) && !is_null($product->getId())) {
            $this->_productModel = $product;
            $this->_productId = $product->getId();

            return;
        }
        $productId = $this->getEmptyProductId();
        $this->_productId = $productId;
        $this->_productModel = $this->createProduct($productId, ['sku' => $sku]);
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
       $this->setAttribute('tax_class_id', $this->_getBehatTaxClass());
    }

    /**
     * @Given The product is not taxable
     */
    public function theProductIsNotTaxable()
    {
        $this->setAttribute('tax_class_id', 0);
    }

    /**
     * @Given The product has the following tiered prices
     */
    public function theProductHasTheFollowingTieredPrices(TableNode $table)
    {
        $tiers = array();

        foreach ($table as $row) {
            $tiers[] = (object) array(
                'website' => 'all',
                'customer_group_id' => 'all',
                'qty' => $row['Min Amount'],
                'price' => $row['Price']
            );
        }

        $tierPriceApi = Mage::getSingleton('catalog/product_attribute_tierprice_api_v2');
        $tierPriceApi->update($this->_productId, $tiers);
    }

    /**
     * @Given /^the product is available$/
     */
    public function theProductIsAvailable()
    {
        $this->setAttribute('status', \Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
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


    /**
     * @Given The product is in the category
     */
    public function theProductIsInTheCategory()
    {
        $product = $this->getTheProduct();
        $product->setCategoryIds($this->_category->getCategoryId());

        $product->save();
    }

    /**
     * @Given /^The price of the product is [\D]+([0-9,.]+)/
     */
    public function thePriceOfTheSimpleProductIs($arg1)
    {
        $product = $this->getTheProduct();
        
        $product->setPrice($arg1);
        $product->save();
    }


    /**
     * @When /^I update the quantity of the product in the cart to (.+)$/
     */
    public function iUpdateTheQuantityOfTheProductInTheCartTo($arg1)
    {
        $this->getSession()->getPage()->find('css', '#shopping-cart-table .input-text.qty')->setValue($arg1);
    }


    /**
     * @Given /^The product is in stock$/
     * Set the stock of the product to in stock
     */
    public function theProductIsInStock()
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getTheProduct()->getId());
        $stockItem->setIsInStock(1);
        $stockItem->setQty(100);
        $stockItem->save();
    }


    /**
     * Check the product price
     * @Then /^The price of the product should be [\D]+([0-9,.]+)/
     */
    public function thePriceOfTheProductShouldBe($arg1)
    {
        $price = $this->getSession()->getPage()->find('css', '.price-box .price');
        if(is_null($price)) {
            throw new \Exception('Could not find the price for the product');
        }
        $val = preg_replace('/[^0-9,.]/','', $price->getText());

        //Remove currency and non numeric characters
        if ($val != $arg1)
        {
            throw new Exception('Price ' . $val . ' does not match ' . $arg1);
        }
    }

    /**
     * @When I add the product to the cart
     */
    public function iAddTheProductToTheCart()
    {
        $this->_cart->iClickTheAddToCartButton('.add-to-cart button');
    }


    /**
     * @Given /^The simple product has a stock of (\d+)$/
     */
    public function theSimpleProductHasAStockOf($arg1)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getTheProduct()->getId());

        $stockItem->setIsInStock(1);
        $stockItem->setQty($arg1);
        $stockItem->save();
    }

    /**
     * @Given /^The product is out of stock$/
     */
    public function theProductIsOutOfStock()
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getTheProduct()->getId());

        $stockItem->setIsInStock(0);
        $stockItem->setQty(0);
        $stockItem->save();
    }
}