<?php
class Ccc_Repricer_Block_Adminhtml_Matching extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_matching';
        $this->_blockGroup = 'repricer';
        $this->_headerText = Mage::helper('repricer')->__('Manage Matching');
        parent::__construct();
        // $this->removeButton('add');
    }
    protected function _beforeSave()
    {
        $data = $this->getData();
        
        unset($data['product_name']);
        unset($data['competitor_name']);
        echo "<pre>";
        print_r($data);
        die();
        $this->setData($data);
        parent::_beforeSave();

        return $this;
    }
}
