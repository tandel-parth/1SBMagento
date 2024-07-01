<?php
class Ccc_Ticketsystem_Model_Resource_Comment extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_ticketsystem/comment', 'comment_id');
    }

}

?>