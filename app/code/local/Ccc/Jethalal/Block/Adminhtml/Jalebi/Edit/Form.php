<?php

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('jalebi_form');
        $this->setTitle(Mage::helper('jethalal')->__('Manage Jalebi'));
        $this->setData("jalebi_save", $this->getUrl('*/*/savenew'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_jethalal_jalebi');
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('jalebi_save'), 'method' => 'post')
        );
        
        // $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

?>