<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Mink;
use EdmondsCommerce\MockServer\MockServer;

class CategoryContextTest extends AbstractTestCase {
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
        $this->server = new MockServer(__DIR__ . '/assets/routers/CategoryRouter.php', $this->getContainerIp(), 8080);
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

    public function testShouldSeeTheProductsInAGrid() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testShouldSeeTheProductsInAList() {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }

    public function testShouldSeeProductsOnThePageShouldFind8Products() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->assertEquals(8, $this->context->iShouldSeeProductsOnThePage(8));
    }

    public function testShouldSeeProductsOnThePageShouldFailOurExpectationOfSeeing9Products() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->iShouldSeeProductsOnThePage(9);
    }

    public function testChangeProductDisplayModeToGridShouldDisplayProductsInAGrid() {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iChangeProductDisplayModeToGrid();

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testChangeProductDisplayModeToListShouldDisplayProductsInAList() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->context->iChangeProductDisplayModeToList();

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }

    public function testSelectTheGridOption() {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectThe('grid');

        $this->assertTrue($this->context->iShouldSeeTheProductsInAGrid());
    }

    public function testSelectTheListOption() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $this->context->iSelectThe('list');

        $this->assertTrue($this->context->iShouldSeeTheProductsInAList());
    }

    public function testSelectTheNonExistentProductDisplayOption() {
        $url = $this->server->getUrl('/products-list-category');

        $this->seleniumSession->visit($url);

        $this->expectException(\Exception::class);

        $this->context->iSelectThe('slideshow');
    }

    public function testSelectToShowProducts() {
        $url = $this->server->getUrl('/products-grid-category');

        $this->seleniumSession->visit($url);

        $productCountInThePage = 12;

        $this->context->iSelectToShowProducts($productCountInThePage);

        $this->assertEquals($productCountInThePage, $this->context->iShouldSeeProductsOnThePage($productCountInThePage));
    }

}