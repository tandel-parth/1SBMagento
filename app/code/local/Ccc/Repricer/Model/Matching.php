<?php
class Ccc_Repricer_Model_Matching extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/matching');
    }
    protected function _beforeSave()
    {
        if ($this->getData('reason') == 0 || $this->getData('reason') == 3) {
            $competitorUrl = $this->getData('competitor_url');
            $competitorSku = $this->getData('competitor_sku');
            $price = $this->getData('competitor_price');

            if (!empty($competitorUrl) && !empty($competitorSku) && $price == 0) {
                // Set reason to 3 (Not Available)
                $this->setData('reason', 3);
            } elseif (!empty($competitorUrl) && !empty($competitorSku) && $price != 0) {
                // Set reason to 1 (Active)
                $this->setData('reason', 1);
            }
        }

        return parent::_beforeSave();
    }
    public function getCollectionData()
    {
        $collection = $this->getCollection();
        $collection->getSelect()
            ->join(
                array('cpev' => Mage::getSingleton('core/resource')->getTableName('ccc_repricer/competitors')),
                'cpev.competitor_id = main_table.competitor_id',
                ['']
            );
        // Add additional join
        $collection->getSelect()
            ->join(
                array('pro' => Mage::getSingleton('core/resource')->getTableName('catalog/product')),
                'main_table.product_id = pro.entity_id',
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
            '0' => 'no match',
            '1' => 'active',
            '2' => 'out of stock',
            '3' => 'not available',
            '4' => 'rong match'
        );
        return $reason;
    }
}

?>