<?php
class Ccc_Repricer_Model_Resource_Matching_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/matching');
    }
    // public function getSize(){
    //     return count($this->getAllIds());
    // }
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $unionSelect = clone $this->getSelect();

        $unionSelect->reset(Zend_Db_Select::ORDER);
        $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $countSelect = clone $this->getSelect();
        $countSelect->reset();
        $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');

        return $countSelect;
    }
}

?>