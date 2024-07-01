<?php
class Ccc_Ticketsystem_Model_Resource_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_ticketsystem/status', 'status_id');
    }

}

?>