<?php
class Ccc_Mostsellingrelateditems_Model_Mostsellingrelateditems extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_mostsellingrelateditems/mostsellingrelateditems');
    }
    public function _afterSave()
    {
        Mage::dispatchEvent('mostsellingrelateditems_after_save', array('most_selling' => $this));
        return parent::_afterSave();
    }
}
