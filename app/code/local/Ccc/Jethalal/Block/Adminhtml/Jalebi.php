<?php
class Ccc_Jethalal_Block_Adminhtml_Jalebi extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // print_r(Mage::getStoreConfig('jethalal'));
        $this->_controller = 'adminhtml_jalebi';
        $this->_blockGroup = 'jethalal';
        $this->_headerText = Mage::helper('jethalal')->__('Jalebi bai');
        parent::__construct();        
    }
}
