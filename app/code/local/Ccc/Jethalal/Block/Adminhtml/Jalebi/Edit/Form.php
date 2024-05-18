<?php

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('jalebi_form');
        $this->setTitle(Mage::helper('jethalal')->__('Manage Jalebi'));
        $this->setData("jalebi_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_jethalal_jalebi');
        $isEdit = ($model && $model->getId());

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('jalebi_save'), 'method' => 'post')
        );

        // $form->setHtmlIdPrefix('block_');

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
            'select',
            array(
                'name' => 'jalebi_type',
                'label' => Mage::helper('jethalal')->__('Jalebi Type'),
                'title' => Mage::helper('jethalal')->__('Jalebi Type'),
                'required' => true,
                'options' => array(
                    "Rabdi Jalebi" => "Rabdi Jalebi",
                    "Khoya Jalebi" => "Khoya Jalebi",
                    "Urad ki Jalebi" => "Urad ki Jalebi",
                    "Imarti Jalebi" => "Imarti Jalebi",
                ),
            )
        );

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
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

?>