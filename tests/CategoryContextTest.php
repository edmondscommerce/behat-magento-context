<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Mink;
use EdmondsCommerce\MockServer\MockServer;

class CategoryContextTest extends AbstractTestCase
{
    /**
     * @var CategoryContext
     */
    private $context;

    /**
     * @var MockServer
     */
    private $server;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        //Set up the mock server
        $this->server = new MockServer(
            __DIR__ . '/assets/routers/CategoryRouter.php',
            __DIR__ . '/assets/routers/httpdocs',
            $this->getContainerIp(),
            8080
        );
        $this->server->startServer();

        $mink = new Mink(['selenium2' => $this->seleniumSession]);
        $mink->setDefaultSessionName('selenium2');
        $this->seleniumSession->start();

        //Set up Mink in the class
        $this->context = new CategoryContext();
        $this->context->setMink($mink);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->seleniumSession->stop();
        $this->server->stopServer();
    }

    public function testShouldSeeTheProductsInAGrid()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testShouldSeeTheProductsInAList()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }


    public function testShouldSeeProductsOnThePageShouldFind8Products()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertEquals(8, $this->context->iShouldSeeProductsOnThePage(8));
    }

    public function testShouldSeeProductsOnThePageShouldFailOurExpectationOfSeeing9Products()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->iShouldSeeProductsOnThePage(9);
    }

    public function testISelectTheShouldChangeTheDiplayModeToGridSuccessfully()
    {


        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iSelectThe('grid'));

        $actualUrl = ($this->seleniumSession->getCurrentUrl());

        $expectedUrl = ($this->server->getUrl('/products-grid-category'));

        $this->assertEquals($expectedUrl, $actualUrl);

    }

    public function testISelectTheShouldChangeTheDiplayModeToListSuccessfully()
    {


        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iSelectThe('list'));

        $actualUrl = ($this->seleniumSession->getCurrentUrl());

        $expectedUrl = ($this->server->getUrl('/products-list-category'));

        $this->assertEquals($expectedUrl, $actualUrl);

    }


    public function testChangeProductDisplayModeToGridShouldDisplayProductsInAGrid()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iChangeProductDisplayModeToGrid();

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testChangeProductDisplayModeToListShouldDisplayProductsInAList()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->context->iChangeProductDisplayModeToList();

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }

    public function testSelectTheGridOption()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectThe('grid');

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testSelectTheListOption()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectThe('list');

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }

    public function testSelectTheNonExistentProductDisplayOption()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->iSelectThe('slideshow');
    }

    public function testSelectToShowProducts()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $productCountInThePage = 24;

        $this->context->iSelectToShowProducts($productCountInThePage);

        $this->assertEquals($productCountInThePage, $this->context->iShouldSeeProductsOnThePage($productCountInThePage));
    }

    public function testAmTestingACategoryWithAnIdOf3()
    {
        $categoryId = 3;

        $this->context->iAmTestingACategoryWithAnIdOf($categoryId);

        $this->assertEquals($categoryId, $this->context->getCategoryId());
    }

    public function testAmOnTheCategoryPage()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iAmOnTheCategoryPage());
    }

    public function testthereAreMultipleProductsInTheCategoryWillFindProducts()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->thereAreMultipleProductsInTheCategory());
    }

    public function testthereAreMultipleProductsInTheCategoryWillFindNoProducts()
    {
        $url = $this->server->getUrl('/products-grid-category-0');

        $this->seleniumSession->visit($url);

        $this->assertFalse($this->context->thereAreMultipleProductsInTheCategory());
    }

    public function testTheProductsAreSortedByPositionAscending()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->theProductsAreSortedByOrderMethodAndDirection('Position'));
    }

    public function testTheProductsAreSortedByNameAscending()
    {
        $url = $this->server->getUrl('/products-grid-category-sort-by-name');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->theProductsAreSortedByOrderMethodAndDirection('Name'));
    }

    public function testTheProductsAreSortedByPriceAscending()
    {
        $url = $this->server->getUrl('/products-grid-category-sort-by-price');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->theProductsAreSortedByOrderMethodAndDirection('Price'));
    }

    public function testTheProductsAreSortedByOrderMethodAndDirectionAndSortBySelectorNotFound()
    {
        $url = $this->server->getUrl('/products-grid-category-sort-by-select-notfound');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->theProductsAreSortedByOrderMethodAndDirection('Price');
    }

    public function testTheProductsAreSortedByOrderMethodAndDirectionSortByOptionNotFound()
    {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->expectException(ElementNotFoundException::class);

        $this->assertTrue($this->context->theProductsAreSortedByOrderMethodAndDirection('InexistantSortBy'));
    }

    public function testIShouldSeeTheProductsInAGridIsSuccessful()
    {

        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInA('grid'));
    }

    public function testIShouldSeeTheProductsInAGridFails()
    {

        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->expectException(ElementNotFoundException::class);

        $this->context->iShouldSeeTheProductsInA('grid');
    }

    public function testIShouldSeeTheProductsInAListIsSuccessful()
    {

        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInA('list'));
    }

    public function testIShouldSeeTheProductsInAListFails()
    {

        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->expectException(ElementNotFoundException::class);

        $this->context->iShouldSeeTheProductsInA('list');
    }

    public function testIShouldSeeTheProductsInUnknownFails()
    {

        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->iShouldSeeTheProductsInA('Unknown');
    }

    public function testISelectToShowProducts()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectToShowProducts(10);

        $productsPerPageResult = $this->context->theLimiterIsSetTo(10);

        $this->assertTrue($productsPerPageResult);
    }

    public function testISelectToShowProductsFails()
    {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectToShowProducts(10);

        $this->expectException(\Exception::class);

        $productsPerPageResult = $this->context->theLimiterIsSetTo(20);
    }

    public function testISelectToShowProductsInvalidPage()
    {
        $url = $this->server->getUrl('/');

        $this->seleniumSession->visit($url);

        $this->expectException(\UnexpectedValueException::class);

        $this->context->iSelectToShowProducts(10);

    }

    public function testTheLimiterIsSetToNoLimiter()
    {
        $url = $this->server->getUrl('/');

        $this->seleniumSession->visit($url);

        $this->expectException(\UnexpectedValueException::class);

        $this->context->theLimiterIsSetTo(10);

    }

}