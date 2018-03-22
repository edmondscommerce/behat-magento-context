<?php

use EdmondsCommerce\BehatMagentoOneContext\ContainerHelper;

require_once __DIR__ . '/../../../vendor/autoload.php';
ini_set('display_errors', 1);



if (preg_match('/\.(?:png|css|jpg|jpeg|gif|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}

$router = new EdmondsCommerce\MockServer\StaticRouter();
$router->addCallbackRoute('/products-grid-category', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsGridCategory.html");

    return str_replace($search, $replace, $subject);
});
$router->addCallbackRoute('/products-grid-category-24', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsGridCategory24Products.html");

    return str_replace($search, $replace, $subject);
});
$router->addCallbackRoute('/products-grid-category-0', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsGridCategory0Products.html");

    return str_replace($search, $replace, $subject);
});
$router->addCallbackRoute('/products-list-category', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsListCategory.html");

    return str_replace($search, $replace, $subject);
});

$router->run()->send();
