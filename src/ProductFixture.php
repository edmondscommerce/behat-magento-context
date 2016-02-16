<?php
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\BehatMagentoOneContext;


use Mage;
use Mage_Catalog_Model_Product_Visibility;

class ProductFixture extends AbstractMagentoContext
{

    protected $_productId;
    /** @var  \Mage_Catalog_Model_Product */
    protected $_productModel;

    public function createProduct($productId, array $data)
    {
        $this->_productId = $productId;
        $product          = Mage::getModel('catalog/product')->load($productId);
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

    public function setAttribute($attributeCode, $value, $productId = null)
    {
        if(is_null($productId)) {
            $productId = $this->_productId;
        }
        if(is_null($productId)) {
            throw new \Exception('No Product ID found');
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if(is_null($product->getSku())) {
            throw new \Exception("No Product with an ID of $productId found");
        }

        $product->setData($attributeCode, $value);
        $product->save();
    }

    public function getProductAttribute($attribute)
    {
        $product = $this->_productModel;
        if(is_null($product->getId())) {
            $product = Mage::getModel('catalog/product')->load($this->_productId);
        }
        if(is_null($product->getId())) {
            throw new \Exception('No Product has been set');
        }

        return $product->getData($attribute);
    }

    protected function _getRequiredProductAttributes()
    {
        return [
            'website_ids'       => [1],
            'attribute_set_id'  => Mage::getModel('catalog/product')->getDefaultAttributeSetId(),
            'type_id'           => 'simple',
            'created_at'        => strtotime('now'),
            'sku'               => 'behat_product',
            'name'              => 'Behat Product',
            'weight'            => 4.0000,
            'status'            => 1,
            'tax_class_id'      => 4,
            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            'price'             => 11.22,
            'cost'              => 22.33,
            'url_key'           => 'behat-product',
            'description'       => 'This is a product used for Behat Testing',
            'short_description' => 'This is a product used for Behat Testing',
            'stock_data'        => [
                'use_config_manage_stock' => 0,
                'manage_stock'            => 1,
                'min_sale_qty'            => 1,
                'is_in_stock'             => 1,
                'qty'                     => 999
            ],
        ];
    }
}