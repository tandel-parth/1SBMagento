<?php

class Ccc_Jethalal_Model_Product extends Mage_Catalog_Model_Product
{
    public function getName()
    {
        $productName = parent::getName();
        $productName = $productName. " Mayank";
        return $productName; 
    }
    public function getFinalPrice()
    {
        // echo "<pre>";
        // print_r(get_class_methods($this));
        $productPrice = parent::getFinalPrice();
        $productPrice = 155;
        return $productPrice; 
    }
    public function getPrice()
    {
        $productPrice = parent::getPrice();
        $productPrice = 252;
        return $productPrice; 
    }
    public function getMinimalPrice()
    {
        $productPrice = parent::getMinimalPrice();
        $productPrice = 252;
        return $productPrice; 
    }
    
}