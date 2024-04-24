<?php

class Ccc_Repricer_Block_Adminhtml_Matching_Grid_Renderer_Productname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $productName = $row->getData('product_name');
        $productSku = $row->getData('product_sku');
        return "<span><b>Product Name: </b>". $productName ." <br> <b>Product Sku: </b>". $productSku ." </span>";
    }
}


?>