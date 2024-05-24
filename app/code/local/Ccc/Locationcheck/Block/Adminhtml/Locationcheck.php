<?php
class Ccc_Locationcheck_Block_Adminhtml_Locationcheck extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_locationcheck';
        $this->_blockGroup = 'locationcheck';
        $this->_headerText = Mage::helper('locationcheck')->__('Manage Locationcheck');
        parent::__construct();
    }
}
