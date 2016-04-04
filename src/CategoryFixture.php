<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Exception;
use Mage;
use Mage_Catalog_Model_Category;

class CategoryFixture extends AbstractMagentoContext
{
    /**
     * @var int
     */
    protected $_categoryId;

    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $_category;

    public function getCategory()
    {
        if (!$this->hasCategory())
        {
            $this->loadCategory();
        }

        return $this->_category;
    }

    protected function setCategoryId($id)
    {
        $this->_categoryId = $id;
    }

    public function getCategoryId()
    {
        return $this->_categoryId;
    }

    /**
     * @throws Exception
     */
    protected function loadCategory()
    {
        if(is_null($this->_categoryId))
        {
            throw new Exception('Category ID has not been set in feature');
        }

        $category = Mage::getModel('catalog/category')->load($this->_categoryId);
        if(!$category->getId())
        {
            throw new Exception('Specified category with ID '.$this->_categoryId.' does not exist');
        }

        $this->_category = $category;
    }

    protected function hasCategory()
    {
        return (!is_null($this->_category));
    }
}