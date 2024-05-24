<?php
class Mage_Eav_Model_Entity_Attribute_Source_Seller extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions(){
        $a = [];
        $collection = Mage::getModel('productseller/seller')->getCollection();
        foreach($collection->getData() as $data){
            $a[$data['id']] = $data['seller_name'];
        }
        return $a;
    }
}

?>