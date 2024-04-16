<?php 
require_once('../app/Mage.php'); //Path to Magento
Mage::app();
$product = Mage::getModel('catalog/product')->getCollection();
$matching = Mage::getModel('ccc_repricer/matching')->getCollection();
$competitors = Mage::getModel('ccc_repricer/competitors')->getCollection();
$entityId = [];
$productId = [];
$competitorId = [];
foreach ($product as $pro){
    $entityId[] = $pro->getEntityId();
}
foreach ($matching as $match){
    $productId[] = $match->getProductId();
}
foreach ($competitors as $comp){
    $competitorId[] = $comp->getCompetitorId();
}
$newProductId=array_diff($entityId, $productId);
echo "<pre>";
// print_r($newProductId);
foreach ($newProductId as $newProduct){
    foreach ($competitorId as $comp){
        $matching = Mage::getModel('ccc_repricer/matching');
        $matching->setData(['product_id' => $newProduct, 'competitor_id' =>$comp]);
        $result = $matching->save();
    }
}
if($result){
    echo "success";
}else{
    echo "Failure";
}