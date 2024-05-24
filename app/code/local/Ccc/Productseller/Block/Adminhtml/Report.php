<?php
class Ccc_Productseller_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'productseller';
        $this->_headerText = Mage::helper('productseller')->__('Manage Seller Report');
        parent::__construct();
        $this->removeButton('add');
        if (Mage::getStoreConfig('ccc_productseller/general/productseller_enabled')) {
            $this->addButton(
                'enable_seller',
                [
                    'label'   => Mage::helper('productseller')->__('Assign to seller'),
                    'class'   => 'enable_seller',
                ]
            );
        }
    }
}
