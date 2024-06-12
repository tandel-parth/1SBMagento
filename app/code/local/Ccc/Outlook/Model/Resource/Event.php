<?php
class Ccc_Outlook_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/event', 'event_id');
    }
}

?>


