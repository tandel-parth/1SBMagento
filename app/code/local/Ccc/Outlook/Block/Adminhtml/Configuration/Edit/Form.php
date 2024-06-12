<?php

class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('configuration_form');
        $this->setTitle(Mage::helper('outlook')->__('Manage Configuration'));
        $this->setData("configuration_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_outlook_configuration');
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('configuration_save'), 'method' => 'post')
        );
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

?>