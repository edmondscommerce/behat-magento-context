<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ElementNotFoundException;
use Exception;
use UnexpectedValueException;

class CategoryContext extends CategoryFixture implements Context, SnippetAcceptingContext
{
    /**
     * @Then I should see the products in a :arg1
     */
    public function iShouldSeeTheProductsInA($arg1)
    {
        if ($arg1 === 'grid') {
            return $this->iShouldSeeTheProductsInAGrid();
        }

        if ($arg1 === 'list') {
            return $this->iShouldSeeTheProductsInAList();
        }

        throw new \Exception('Unknown product display type: ' . $arg1);
    }

    /**
     * @Then The products should be displayed in a grid
     */
    public function iShouldSeeTheProductsInAGrid()
    {
        $gridExists = $this->getSession()->getPage()->has('css', 'ul.products-grid');

        if ($gridExists === false) {

            throw new ElementNotFoundException($this->getSession()->getDriver(), 'css', 'ul.products-grid');
        }

        return $gridExists;
    }


    /**
     * @Then The products should be displayed in a list
     */
    public function iShouldSeeTheProductsInAList()
    {
        $listExists = $this->getSession()->getPage()->has('css', 'ol.products-list');

        if ($listExists === false) {

            throw new ElementNotFoundException($this->getSession()->getDriver(), 'css', 'ol.products-list');
        }

        return $listExists;
    }


    /**
     * @Then /^I should see (\d+) products on the page$/
     */
    public function iShouldSeeProductsOnThePage($arg1)
    {
        $productNames = $this->getSession()->getPage()->findAll('css', '.product-name');
        $count = count($productNames);
        if ($count !== $arg1) {
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
                return $this->iChangeProductDisplayModeToGrid();
                break;
            case 'list':
                return $this->iChangeProductDisplayModeToList();
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

        return true;
    }

    /**
     * @When I change product view mode to list
     */
    public function iChangeProductDisplayModeToList()
    {
        $this->getSession()->getPage()->find('css', '.sorter .view-mode a.list')->click();

        return true;
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
            throw new UnexpectedValueException('Results per page limiter not found.');
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
        return (bool)count($this->getSession()->getPage()->findAll('css', '.product-name'));
    }


    /**
     * @Given The products are sorted by :order and :direction direction
     */
    public function theProductsAreSortedByOrderMethodAndDirection($order, $direction = 'asc')
    {
        $page = $this->getSession()->getPage();
        $select = $page->find('css', '.sorter .sort-by select');
        if (null === $select) {
            throw new Exception('Sort by selector not found');
        }

        $select->selectOption($order);

        // $select->isSelected() doesn't seem to work, therefore doing a workaround using containsText function
        $optionValue = $this->containsText(strtolower($order), $select->getValue());

        $directionSwitcher = $page->find('css', '.sorter .sort-by .sort-by-switcher');
        if (null === $directionSwitcher) {
            throw new Exception('Sort by switcher not found');
        }

        $directionSwitcherClass = $directionSwitcher->getAttribute('class');
        $directionValue = $this->containsText('sort-by-switcher--' . $direction, $directionSwitcherClass);

        return $optionValue === true && $directionValue === true;
    }

    public function containsText($needle, $haystack)
    {
        return mb_strpos($haystack, $needle) !== false;
    }

    /**
     * @Given /^I am on the category page$/
     */
    public function iAmOnTheCategoryPage()
    {
        return $this->getSession()->getPage()->has('css', 'body.catalog-category-view');
    }
}