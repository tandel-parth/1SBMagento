<?php
class Ccc_Repricer_Model_Matching extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/matching');
    }
    protected function _beforeSave()
    {
        $data = $this->getData();
        
        unset($data['product_name']);
        unset($data['competitor_name']);
        $this->setData($data);
        parent::_beforeSave();

        return $this;
    }

}

?>
