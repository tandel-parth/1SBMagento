<?php

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Edit_Tabs_Generalform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_jethalal_jalebi');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('jalebi_save'), 'method' => 'post')
        );


        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('jethalal')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getJalebiId()) {
            $fieldset->addField(
                'jalebi_id',
                'hidden',
                array(
                    'name' => 'jalebi_id',
                )
            );
        }
        $fieldset->addField(
            'jalebi_type',
            'text',
            array(
                'name' => 'jalebi_type',
                'label' => Mage::helper('jethalal')->__('Jalebi Type'),
                'title' => Mage::helper('jethalal')->__('Jalebi Type'),
                'required' => true,
            )
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
