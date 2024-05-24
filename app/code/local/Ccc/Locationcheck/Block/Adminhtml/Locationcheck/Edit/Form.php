<?php

class Ccc_Locationcheck_Block_Adminhtml_Locationcheck_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('locationcheck_form');
        $this->setTitle(Mage::helper('locationcheck')->__('Manage Location'));
        $this->setData("locationcheck_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_location_check');
        $isEdit = ($model && $model->getId());


        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('locationcheck_save'), 'method' => 'post')
        );

        // $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('locationcheck')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($isEdit) {
            $fieldset->addField(
                'id',
                'hidden',
                array(
                    'name' => 'id',
                )
            );
        }
        $fieldset->addField(
            'zipcode',
            'text',
            array(
                'name' => 'zipcode',
                'label' => Mage::helper('locationcheck')->__('Location Zipcode'),
                'title' => Mage::helper('locationcheck')->__('Location Zipcode'),
                'required' => true,
            )
        );


        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => Mage::helper('locationcheck')->__('Active'),
                'title' => Mage::helper('locationcheck')->__('Active'),
                'name' => 'is_active',
                'required' => true,
                'options' => array(
                    "hidden" => Mage::helper('locationcheck')->__('---Select---'),
                    1 => Mage::helper('locationcheck')->__('YES'),
                    2 => Mage::helper('locationcheck')->__('NO')
                ),
            )
        );

        if ($isEdit) {
            $fieldset->addField(
                'created_at',
                'hidden',
                array(
                    'name' => 'created_at',
                    'label' => Mage::helper('locationcheck')->__('Created Date'),
                    'title' => Mage::helper('locationcheck')->__('Created Date'),
                )
            );
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
