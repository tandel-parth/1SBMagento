<?php
class Ccc_Jethalal_Block_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        //Limit the number of products to 2
        // echo 123;
        // var_dump(get_class($this));
        $collection = parent::_getProductCollection();
        $collection->setPageSize(2);
        return $collection;
    }
}