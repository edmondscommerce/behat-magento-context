<?php namespace EdmondsCommerce\BehatMagentoOneContext;

/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

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
     * @param       $productId
     * @param array $data
     *
     * @return \EdmondsCommerce_FeaturedProducts_Model_Catalog_Product|Mage_Catalog_Model_Product
     * @throws Exception
     */
    public function createProduct($productId, array $data)
    {
        $this->_productId = $productId;
        $product          = Mage::getModel('catalog/product')->load($productId);

        #$product->unsetData();
        if (is_null($product->getId())) {
            $product->setId($productId);
        }
        $requiredAttribute = $this->_getRequiredProductAttributes();
        foreach ($requiredAttribute as $key => $value) {
            $product->setData($key, $value);
        }
        foreach ($data as $key => $value) {
            $product->setData($key, $value);
        }

        // Prevent options being saved multiple times
        $product->getOptionInstance()->unsetOptions();
        $product->save();
        $this->_productModel = $product;
        Mage::app()->cleanCache();

        return $product;
    }

    public function getEmptyProductId($productId = null)
    {
        if(is_null($productId)) {
            $productId = 999999;
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if(is_null($product->getSku())) {
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
        if (is_null($this->_productId)) {
            throw new Exception('The product under test has not been set');
        }

        if ($this->_productModel) {
            return $this->_productModel;
        } else {
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
        if (is_null($productId)) {
            $productId = $this->_productId;
        }
        if (is_null($productId)) {
            throw new \Exception('No Product ID found');
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (is_null($product->getSku())) {
            throw new \Exception("No Product with an ID of $productId found");
        }
        
        if(!is_null($this->_storeId)) {
            $product->setStoreId($this->_storeId);
        }

        $product->setData($attributeCode, $value);
        $product->save();
    }

    public function getProductAttribute($attribute)
    {
        $product = $this->_productModel;
        if (is_null($product->getId())) {
            $product = Mage::getModel('catalog/product')->load($this->_productId);
        }
        if (is_null($product->getId())) {
            throw new \Exception('No Product has been set');
        }

        return $product->getData($attribute);
    }

    protected function _getRequiredProductAttributes()
    {
        return [
            'website_ids'       => Mage::getResourceModel('core/website_collection')->getAllIds(),
            'attribute_set_id'  => Mage::getModel('catalog/product')->getDefaultAttributeSetId(),
            'type_id'           => 'simple',
            'created_at'        => strtotime('now'),
            'sku'               => 'behat_product',
            'name'              => 'Behat Product',
            'weight'            => 4.0000,
            'status'            => 1,
            'tax_class_id'      => $this->_getBehatTaxClass(),
            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price'             => 11.22,
            'cost'              => 22.33,
            'url_key'           => 'behat-product',
            'description'       => 'This is a product used for Behat Testing',
            'short_description' => 'This is a product used for Behat Testing',
            'stock_data'        => [
                'use_config_manage_stock' => 1,
                'manage_stock'            => 1,
                'min_sale_qty'            => 1,
                'is_in_stock'             => 1,
                'qty'                     => 999
            ],
        ];
    }

    protected function _getBehatTaxClass()
    {
        $taxClass = Mage::getModel('tax/class')->getCollection()->addFieldToFilter('class_name', 'Behat Tax Rate')
                        ->addFieldToFilter('class_type', 'PRODUCT')->getFirstItem();
        if (is_null($taxClass->getId())) {
            $taxClass->setData('class_name', 'Behat Tax Rate')->setData('class_type', 'PRODUCT');
            $taxClass->save();
        }

        return $taxClass->getId();
    }
}