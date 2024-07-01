<?php
class Ccc_Ticketsystem_Model_Resource_Ticketsystem extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_ticketsystem/ticketsystem', 'ticket_id');
    }

}

?>