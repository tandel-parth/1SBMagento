<?php
class Ccc_Productseller_Block_Adminhtml_Productseller extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_productseller';
        $this->_blockGroup = 'productseller';
        $this->_headerText = Mage::helper('productseller')->__('Manage Productseller');
        parent::__construct();
    }
}
