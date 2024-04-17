<?php
require_once ('../app/Mage.php'); //Path to Magento
Mage::app();

$matchingModel = Mage::getModel('ccc_repricer/matching');
$competitorsModel = Mage::getModel('ccc_repricer/competitors');
$productModel = Mage::getModel('catalog/product');
$entityId = [];
foreach ($productModel->getCollection() as $pro) {
    $entityId[] = $pro->getEntityId();
}
$productId = [];
foreach ($matchingModel->getCollection() as $match) {
    $productId[] = $match->getProductId();
}
foreach ($competitorsModel->getCollection() as $comp) {
    echo 223;
    $competitorId[] = $comp->getCompetitorId();
}

$newProductId = array_diff($entityId, $productId);
print_r($newProductId);

