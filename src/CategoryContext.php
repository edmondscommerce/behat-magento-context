<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Exception;

class CategoryContext extends CategoryFixture implements Context, SnippetAcceptingContext
{
    /**
     * @Then I should see the products in a :arg1
     */
    public function iShouldSeeTheProductsInA($arg1)
    {
        if ($arg1 === 'grid')
        {
            $this->iShouldSeeTheProductsInAGrid();
        }

        if ($arg1 === 'list')
        {
            $this->iShouldSeeTheProductsInAList();
        }

        throw new \Exception('Unknown product display type: ' . $arg1);
    }

    /**
     * @Then The products should be displayed in a grid
     */
    public function iShouldSeeTheProductsInAGrid()
    {
        return $this->getSession()->getPage()->has('css', 'ul.products-grid');
    }

    /**
     * @Then The products should be displayed in a list
     */
    public function iShouldSeeTheProductsInAList()
    {
        return $this->getSession()->getPage()->has('css', 'ol.products-list');
    }


    /**
     * @Then /^I should see (\d+) products on the page$/
     */
    public function iShouldSeeProductsOnThePage($arg1)
    {
        $productNames = $this->getSession()->getPage()->findAll('css', '.product-name');
        $count = count($productNames);
        if ($count !== $arg1)
        {
            throw new Exception('There are ' . $count . ' products on the page but we expected ' . $arg1);
        }

        return $count;
    }

    /**
     * @When I select the :arg1 option
     */
    public function iSelectThe($arg1)
    {
        switch ($arg1) {
            case 'grid':
                $this->iChangeProductDisplayModeToGrid();
                break;
            case 'list':
                $this->iChangeProductDisplayModeToList();
                break;
            default:
                throw new Exception('Unknown product display mode ' . $arg1);
                break;
        }
    }

    /**
     * @When I change product view mode to grid
     */
    public function iChangeProductDisplayModeToGrid()
    {
        $this->getSession()->getPage()->find('css', '.sorter .view-mode a.grid')->click();
    }

    /**
     * @When I change product view mode to list
     */
    public function iChangeProductDisplayModeToList()
    {
        $this->getSession()->getPage()->find('css', '.sorter .view-mode a.list')->click();
    }


    /**
     * @Given /^I am limiting the category to (\d+) products$/
     * @When /^I select to show (\d+) products$/
     * @throws Exception
     */
    public function iSelectToShowProducts($arg1)
    {
        $limiter = $this->getSession()->getPage()->find('css', '.limiter select');

        if (null === $limiter) {
            throw new \UnexpectedValueException('Results per page limiter not found.');
        }

        $limiter->selectOption($arg1);
    }
    
    /**
     * @Given I am testing a category with an ID of :arg1
     */
    public function iAmTestingACategoryWithAnIdOf($arg1)
    {
        $this->setCategoryId($arg1);
    }

    /**
     * @Given There are multiple products in the category
     */
    public function thereAreMultipleProductsInTheCategory()
    {
        return (bool) count($this->getSession()->getPage()->findAll('css', '.product-name'));
    }


    /**
     * @Given I am viewing the category as a :arg1
     */
    public function iAmViewingTheCategoryAsA($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^The products are sorted by (.*) ([^ ]*)$/
     */
    public function theProductsAreSortedByPositionAscending()
    {
        throw new PendingException();
    }

    /**
     * @Then There should be a product listed first in the grid
     */
    public function thereShouldBeAProductListedFirstInTheGrid()
    {
        throw new PendingException();
    }


    /**
     * @Then There should be a new product listed first in the grid
     */
    public function thereShouldBeANewProductListedFirstInTheGrid()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I am on the category page$/
     */
    public function iAmOnTheCategoryPage()
    {
        return $this->getSession()->getPage()->has('css', 'body.catalog-category-view');
    }
}