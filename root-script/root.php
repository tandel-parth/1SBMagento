<?php
require_once('../app/Mage.php'); //Path to Magento
Mage::app();
echo "<pre>";
// $product = Mage::getModel('catalog/product')->load(900);
// print_r($product->getData());
$collection = Mage::getModel('sales/order')->getCollection();
$collection->getSelect()
    ->join(
        array('SFOA' => Mage::getSingleton('core/resource')->getTableName('sales/order_address')),
        'SFOA.parent_id = main_table.entity_id',
        ['postcode As zipcode']
    );
    $data= $collection->getData();
    foreach ($data as $arr){
        echo "<b>Order Id:</b> ";
        echo $arr['entity_id'];
        echo "&nbsp;&nbsp;<b>Zipcode:</b> ";
        echo $arr['zipcode'];
        echo "<br>";    
    }
