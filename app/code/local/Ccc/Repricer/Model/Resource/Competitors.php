<?php
class Ccc_Repricer_Model_Resource_Competitors extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/competitors', 'competitor_id');
    }

}

?>