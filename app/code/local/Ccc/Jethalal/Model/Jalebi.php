<?php
class Ccc_Jethalal_Model_Jalebi extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_jethalal/jalebi');
    }
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'HELLO'],
            ['value' => '2', 'label' => 'HEY'],
            ['value' => '3', 'label' => 'HII']
        ];
    }
}

?>
