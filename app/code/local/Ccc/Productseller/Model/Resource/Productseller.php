<?php
class Ccc_Productseller_Model_Resource_Productseller extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('Ccc_Productseller/productseller', 'id');
    }

}

?>