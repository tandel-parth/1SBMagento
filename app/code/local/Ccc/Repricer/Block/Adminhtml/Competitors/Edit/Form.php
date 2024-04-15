<?php

class Ccc_Repricer_Block_Adminhtml_Competitors_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('competitor_form');
        $this->setTitle(Mage::helper('repricer')->__('Manage Competitors'));
        $this->setData("competitor_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_repricer_competitor');
        $isEdit = ($model && $model->getId());

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('competitor_save'), 'method' => 'post')
        );

        // $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('repricer')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getCompetitorId()) {
            $fieldset->addField(
                'competitor_id',
                'hidden',
                array(
                    'name' => 'competitor_id',
                )
            );
        }
        $fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => Mage::helper('repricer')->__('Competitor Name'),
                'title' => Mage::helper('repricer')->__('Competitor Name'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'url',
            'text',
            array(
                'name' => 'url',
                'label' => Mage::helper('repricer')->__('Competitor URL'),
                'title' => Mage::helper('repricer')->__('Competitor URL'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('repricer')->__('Status'),
                'title' => Mage::helper('repricer')->__('Status'),
                'name' => 'status',
                'required' => true,
                'options' => Mage::getSingleton('ccc_repricer/status')->getOptionArray(),
            )
        );

        if (!$isEdit) {
            $model->setData('status',Mage::getSingleton('ccc_repricer/status')::STATUS_DEFUALT);
        }

        $fieldset->addField(
            'filename',
            'text',
            array(
                'name' => 'filename',
                'label' => Mage::helper('repricer')->__('Competitor Filename'),
                'title' => Mage::helper('repricer')->__('Competitor Filename'),
                'required' => true,
            )
        );
        if ($isEdit) {
            $fieldset->addField(
                'created_date',
                'hidden',
                array(
                    'name' => 'created_date',
                    'label' => Mage::helper('repricer')->__('Competitor Created Date'),
                    'title' => Mage::helper('repricer')->__('Competitor Created Date'),
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