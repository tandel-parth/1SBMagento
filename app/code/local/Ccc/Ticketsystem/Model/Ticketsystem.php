<?php
class Ccc_Ticketsystem_Model_Ticketsystem extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_ticketsystem/ticketsystem');
    }
    public function getTableDataWithFilters($filters){
        $collection = $this->getCollection();
        foreach($filters as $key => $value){
            if(is_array($value)){
                $collection->addFieldToFilter($key, ['in' => $value]);
            }
        }
        return $collection->getData();
    }
}