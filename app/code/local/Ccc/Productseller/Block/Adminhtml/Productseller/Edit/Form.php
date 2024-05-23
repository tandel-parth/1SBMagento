<?php

class Ccc_Productseller_Block_Adminhtml_Productseller_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productseller_form');
        $this->setTitle(Mage::helper('productseller')->__('Manage Seller'));
        $this->setData("productseller_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_seller');
        $isEdit = ($model && $model->getId());


        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('productseller_save'), 'method' => 'post')
        );

        // $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('productseller')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                array(
                    'name' => 'id',
                )
            );
        }
        $fieldset->addField(
            'seller_name',
            'text',
            array(
                'name' => 'seller_name',
                'label' => Mage::helper('productseller')->__('Seller Name'),
                'title' => Mage::helper('productseller')->__('Seller Name'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'company_name',
            'text',
            array(
                'name' => 'company_name',
                'label' => Mage::helper('productseller')->__('Company Name'),
                'title' => Mage::helper('productseller')->__('Company Name'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'address',
            'text',
            array(
                'name' => 'address',
                'label' => Mage::helper('productseller')->__('Address'),
                'title' => Mage::helper('productseller')->__('Address'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'city',
            'text',
            array(
                'name' => 'city',
                'label' => Mage::helper('productseller')->__('City'),
                'title' => Mage::helper('productseller')->__('Address'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'state',
            'text',
            array(
                'name' => 'state',
                'label' => Mage::helper('productseller')->__('State'),
                'title' => Mage::helper('productseller')->__('State'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'county',
            'text',
            array(
                'name' => 'county',
                'label' => Mage::helper('productseller')->__('Country'),
                'title' => Mage::helper('productseller')->__('Country'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => Mage::helper('productseller')->__('Active'),
                'title' => Mage::helper('productseller')->__('Active'),
                'name' => 'is_active',
                'required' => true,
                'options' => array(
                    1 => Mage::helper('productseller')->__('YES'),
                    2 => Mage::helper('productseller')->__('NO')
                ),
            )
        );

        if ($isEdit) {
            $fieldset->addField(
                'created_at',
                'hidden',
                array(
                    'name' => 'created_at',
                    'label' => Mage::helper('productseller')->__('Created Date'),
                    'title' => Mage::helper('productseller')->__('Created Date'),
                )
            );
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
