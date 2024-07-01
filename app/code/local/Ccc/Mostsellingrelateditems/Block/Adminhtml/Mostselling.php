<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_mostselling';
        $this->_blockGroup = 'mostsellingrelateditems';
        $this->_headerText = Mage::helper('mostsellingrelateditems')->__('Manage Grid');
        parent::__construct();
    }
}
