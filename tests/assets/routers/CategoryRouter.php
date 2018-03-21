<?php

use EdmondsCommerce\BehatMagentoOneContext\ContainerHelper;

require_once __DIR__ . '/../../../vendor/autoload.php';
ini_set('display_errors', 1);


$router = new EdmondsCommerce\MockServer\StaticRouter();
$router->addCallbackRoute('/products-grid-category', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsGridCategory.html");

    return str_replace($search, $replace, $subject);
});
$router->addCallbackRoute('/products-list-category', '', function () {
    $search = ['IP_ADDRESS'];
    $replace = [ContainerHelper::getContainerIp()];
    $subject = file_get_contents(__DIR__."/html/Category/ProductsListCategory.html");

    return str_replace($search, $replace, $subject);
});

$router->run()->send();
