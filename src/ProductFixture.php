<?php namespace EdmondsCommerce\BehatMagentoOneContext;

/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

use Behat\Behat\Tester\Exception\PendingException;
use Exception;
use Mage;
use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Product_Visibility;

class ProductFixture extends AbstractMagentoContext
{
    /** @var  int The ID of the product under test */
    protected $_productId;
    /** @var  \Mage_Catalog_Model_Product */
    protected $_productModel;
    protected $_storeId;

    /**
     * @param        $productId
     * @param array  $data
     *
     * @param string $type
     *
     * @return \EdmondsCommerce_FeaturedProducts_Model_Catalog_Product|Mage_Catalog_Model_Product
     * @throws Exception
     */
    public function createProduct($productId, array $data, $type = 'simple')
    {
        $this->_productId = $productId;
        $product          = Mage::getModel('catalog/product')->load($productId);

        //Emulate the admin
        $emulator   = Mage::getSingleton('core/app_emulation');
        $initialEnv = $emulator->startEnvironmentEmulation(0);

        #$product->unsetData();
        if (is_null($product->getId()))
        {
            $product->setId($productId);
        }
        $requiredAttribute = $this->_getRequiredProductAttributes($type);
        foreach ($requiredAttribute as $key => $value)
        {
            $product->setData($key, $value);
        }

        foreach ($data as $key => $value)
        {
            $product->setData($key, $value);
        }

        //Add to all websites by default
        $websiteIds = array_map(function (\Mage_Core_Model_Website $website)
        {
            return $website->getId();
        }, Mage::app()->getWebsites());
        $product->setWebsiteIds($websiteIds);

        // Prevent options being saved multiple times
        $product->getOptionInstance()->unsetOptions();
        //$product->setOrigData();
        $product->save();

        $this->_productModel = $product;
        $this->indexProduct($product);

        Mage::app()->cleanCache();

        //Done, end emulation
        $emulator->stopEnvironmentEmulation($initialEnv);

        return $product;
    }

    /**
     * Indexes the product and reloads the product
     * @param Mage_Catalog_Model_Product $product
     */
    public function indexProduct(\Mage_Catalog_Model_Product $product)
    {
        //Index the product
        $event = Mage::getSingleton('index/indexer')->logEvent($product,
            $product->getResource()->getType(),
            \Mage_Index_Model_Event::TYPE_SAVE,
            false);

        Mage::getSingleton('index/indexer')->getProcessByCode('catalog_url')
            ->setMode(\Mage_Index_Model_Process::MODE_REAL_TIME)
            ->processEvent($event);

        //Reloads the product's attributes
        $product->load($product->getId());
    }

    public function getEmptyProductId($productId = null)
    {
        if (is_null($productId))
        {
            $productId = 999999;
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (is_null($product->getSku()))
        {
            return $productId;
        }

        $productId++;
        return $this->getEmptyProductId($productId);
    }

    /**
     * Get the product under test defined in the feature
     *
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    public function getTheProduct()
    {
        if (is_null($this->_productId))
        {
            throw new Exception('The product under test has not been set');
        }

        if ($this->_productModel)
        {
            return $this->_productModel;
        } else
        {
            throw new Exception("The product under test does not exist");
        }


    }

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * @param      $attributeCode
     * @param      $value
     * @param null $productId
     *
     * @throws Exception
     */
    public function setAttribute($attributeCode, $value, $productId = null)
    {
        if (is_null($productId))
        {
            $productId = $this->_productId;
        }
        if (is_null($productId))
        {
            throw new \Exception('No Product ID found');
        }

        $product = $this->_productModel;
        if ($product === null) {
            $store = Mage::app()->getStore()->getStoreId();
            Mage::app()->setCurrentStore(0);
            $product = Mage::getModel('catalog/product')->load($productId);
            Mage::app()->setCurrentStore($store);
        }

        if (is_null($product->getSku()))
        {
            throw new \Exception("No Product with an ID of $productId found");
        }

        /* If we are testing a specific store, let's avoid any issues around scope and just save the attribute */
        if (!is_null($this->_storeId))
        {
            Mage::getSingleton('catalog/product_action')->updateAttributes(
                array($productId),
                array($attributeCode => $value),
                $this->_storeId
            );

            return;
        }

        $product->setData($attributeCode, $value);
        $store = Mage::app()->getStore()->getStoreId();
        Mage::app()->setCurrentStore(0);
        $product->save();
        Mage::app()->setCurrentStore($store);
    }

    public function getProductAttribute($attribute)
    {
        $product = $this->_productModel;
        if (is_null($product->getId()))
        {
            $product = Mage::getModel('catalog/product')->load($this->_productId);
        }
        if (is_null($product->getId()))
        {
            throw new \Exception('No Product has been set');
        }

        return $product->getData($attribute);
    }

    protected function _getRequiredProductAttributes($type = 'simple')
    {
        return [
            'website_ids'       => Mage::getResourceModel('core/website_collection')->getAllIds(),
            'attribute_set_id'  => Mage::getModel('catalog/product')->getDefaultAttributeSetId(),
            'type_id'           => $type,
            'created_at'        => strtotime('now'),
            'sku'               => 'behat_product_'.$type,
            'name'              => 'Behat Product '.$type,
            'weight'            => 4.0000,
            'status'            => \Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
            'tax_class_id'      => $this->_getBehatTaxClass(),
            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price'             => 11.22,
            'cost'              => 22.33,
            'url_key'           => 'behat-product-'.$type,
            'description'       => 'This is a product used for Behat Testing',
            'short_description' => 'This is a product used for Behat Testing',
            'stock_data'        => [
                'use_config_manage_stock' => 1,
                'manage_stock'            => 1,
                'min_sale_qty'            => 1,
                'is_in_stock'             => 1,
                'qty'                     => 999,
            ],
        ];
    }

    protected function _getBehatTaxClass()
    {
        $taxClass = Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_name', 'Behat Tax Rate')
            ->addFieldToFilter('class_type', 'PRODUCT')->getFirstItem();
        if (is_null($taxClass->getId()))
        {
            $taxClass->setData('class_name', 'Behat Tax Rate')->setData('class_type', 'PRODUCT');
            $taxClass->save();
        }

        return $taxClass->getId();
    }


}