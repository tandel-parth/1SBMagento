<?php
class Ccc_Ticketsystem_Block_Adminhtml_Ticketsystem extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_ticketsystem';
        $this->_blockGroup = 'ticketsystem';
        $this->_headerText = Mage::helper('ticketsystem')->__('Ticket System');
        parent::__construct();
        $this->removeButton('add');
    }
}
