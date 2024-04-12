<?php
class Ccc_Repricer_Block_Adminhtml_Repricer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_repricer';
        $this->_blockGroup = 'repricer';
        $this->_headerText = Mage::helper('repricer')->__('Manage repricer');
        parent::__construct();
        // if (!Mage::getSingleton('admin/session')->isAllowed('ccc_banner/new')) {
        //     $this->removeButton('add');
        // }
    }
}
