<?php

class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tabs_Generalform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_outlook_configuration');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('configuration_save'), 'method' => 'post')
        );


        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('outlook')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getId()) {
            $fieldset->addField(
                'configration_id',
                'hidden',
                array(
                    'name' => 'cofiguration[configration_id]',
                )
            );
        }
        $fieldset->addField(
            'username',
            'text',
            array(
                'name' => 'cofiguration[username]',
                'label' => Mage::helper('outlook')->__('Username'),
                'title' => Mage::helper('outlook')->__('Username'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'email',
            'text',
            array(
                'name' => 'cofiguration[email]',
                'label' => Mage::helper('outlook')->__('User Email'),
                'title' => Mage::helper('outlook')->__('User Email'),
                'required' => true,
            )
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
