<?php

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Edit_Tabs_Extraform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_jethalal_jalebi');
        $isEdit = ($model && $model->getId());

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('jalebi_save'), 'method' => 'post')
        );


        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('jethalal')->__('General Information'), 'class' => 'fieldset-wide'));

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('jethalal')->__('Status'),
                'title' => Mage::helper('jethalal')->__('Status'),
                'name' => 'status',
                'required' => true,
                'options' => array(
                    "1" => "Enabled",
                    "2" => "Disabled",
                ),
            )
        );

        if ($isEdit) {
            $fieldset->addField(
                'created_date',
                'hidden',
                array(
                    'name' => 'created_date',
                    'label' => Mage::helper('jethalal')->__('Jalebi Created Date'),
                    'title' => Mage::helper('jethalal')->__('Jalebi Created Date'),
                )
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
