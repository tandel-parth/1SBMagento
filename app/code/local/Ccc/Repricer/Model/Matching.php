<?php
class Ccc_Repricer_Model_Matching extends Mage_Core_Model_Abstract
{
    public const CONST_REASON_ACTIVE = 1;
    public const CONST_REASON_NO_OUT_OF_STOCK = 2;
    public const CONST_REASON_NOT_AVAILABLE = 3;
    public const CONST_REASON_NO_WRONG_MATCH = 4;
    public const CONST_REASON_NO_MATCH = 5;
    protected function _construct()
    {
        $this->_init('ccc_repricer/matching');
    }
    public function getCollectionData()
    {
        $collection = $this->getCollection();
        $collection->getSelect()
            ->join(
                array('pro' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
                'main_table.product_id = pro.entity_id',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                'main_table.competitor_id = cpev.competitor_id',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('et' => Mage::getSingleton('core/resource')->getTableName('eav/attribute')),
                'et.entity_type_id = pro.entity_type_id && et.attribute_code = "name"',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('at' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar')),
                'at.attribute_id = et.attribute_id AND product_id = at.entity_id AND at.store_id = 0',
                ['']
            );
        $collection->getSelect()
            ->join(
                array('sta' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_int')),
                'sta.entity_id = pro.entity_id AND sta.attribute_id = 96 AND sta.value = 1 AND sta.store_id = 0',
                ['']
            );
        return $collection;
    }
    public function getReasons()
    {
        $reason = array(
            '1' => 'active',
            '2' => 'out of stock',
            '3' => 'not available',
            '4' => 'wrong match',
            '5' => 'no match',
        );
        return $reason;
    }

}

?>