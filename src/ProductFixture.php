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
    private $productId;

    /** @var Mage_Catalog_Model_Product The product under test */
    private $product;

    /**
     * @param $productId
     * @param array $data
     * @return \EdmondsCommerce_FeaturedProducts_Model_Catalog_Product|Mage_Catalog_Model_Product
     * @throws Exception
     */
    public function createProduct($productId, array $data)
    {
        $this->productId = $productId;
        $product = Mage::getModel('catalog/product')->load($productId);

        if (is_null($product->getId()))
        {
            $product->setId($productId);
        }

        $requiredAttribute = $this->_getRequiredProductAttributes();
        foreach ($requiredAttribute as $key => $value)
        {
            $product->setData($key, $value);
        }
        foreach ($data as $key => $value)
        {
            $product->setData($key, $value);
        }

        // Prevent options being saved multiple times
        $product->getOptionInstance()->unsetOptions();
        $product->save();
        $this->product = $product;
        Mage::app()->cleanCache();

        return $product;
    }

    /**
     * Get the product under test defined in the feature
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    public function getTheProduct()
    {
        if (is_null($this->productId))
        {
            throw new Exception('The product under test has not been set');
        }

        if ($this->product)
        {
            return $this->product;
        }

        /** @var Mage_Catalog_Model_Product|null $product */
        $this->loadProduct($this->productId);

        if(!$this->product || !$this->product->getId())
        {
            throw new Exception("The product under test does not exist");
        }

        return $this->product;
    }

    /**
     * @param $attributeCode
     * @param $value
     * @param null $productId
     * @throws Exception
     */
    public function setAttribute($attributeCode, $value, $productId = null)
    {
        if(is_null($productId))
        {
            $product = $this->getTheProduct();
        }
        else
        {
            $product = $this->loadProduct($productId);
        }

        $product->setData($attributeCode, $value);
        $product->save();
    }

    /**
     * @param $id
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    protected function loadProduct($id)
    {
        $product = Mage::getModel('catalog/product')->load($id);

        if (is_null($product->getSku()))
        {
            throw new Exception('No Product with an ID of '.$this->productId.' found');
        }

        if (!$product)
        {
            throw new Exception('The product under test does not exist');
        }

        $this->product = $product;
    }

    public function getProductAttribute($attributeCode)
    {
        $product = $this->getTheProduct();

        return $product->getData($attributeCode);
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