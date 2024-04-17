<?php

class Ccc_Repricer_Block_Adminhtml_Matching_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('matching_form');
        $this->setTitle(Mage::helper('repricer')->__('Manage Matching'));
        $this->setData("matching_save", $this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_repricer_matching');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('matching_save'), 'method' => 'post')
        );

        // $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('repricer')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getRepricerId()) {
            $fieldset->addField(
                'repricer_id',
                'hidden',
                array(
                    'name' => 'repricer_id',
                )
            );
        }
        $fieldset->addField(
            'product_id',
            'hidden',
            array(
                'name' => 'product_id',
                'label' => Mage::helper('repricer')->__('Product Id'),
                'title' => Mage::helper('repricer')->__('Product Id'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'product_name',
            'text',
            array(
                'name' => 'product_name',
                'label' => Mage::helper('repricer')->__('Product Name'),
                'title' => Mage::helper('repricer')->__('Product Name'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'competitor_id',
            'hidden',
            array(
                'name' => 'competitor_id',
                'label' => Mage::helper('repricer')->__('Competitor Id'),
                'title' => Mage::helper('repricer')->__('Competitor Id'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'competitor_name',
            'text',
            array(
                'name' => 'competitor_name',
                'label' => Mage::helper('repricer')->__('Competitor Name'),
                'title' => Mage::helper('repricer')->__('Competitor Name'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'competitor_url',
            'text',
            array(
                'name' => 'competitor_url',
                'label' => Mage::helper('repricer')->__('Competitor URL'),
                'title' => Mage::helper('repricer')->__('Competitor URL'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'competitor_sku',
            'text',
            array(
                'name' => 'competitor_sku',
                'label' => Mage::helper('repricer')->__('Competitor SKU'),
                'title' => Mage::helper('repricer')->__('Competitor SKU'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'competitor_price',
            'text',
            array(
                'name' => 'competitor_price',
                'label' => Mage::helper('repricer')->__('Competitor Price'),
                'title' => Mage::helper('repricer')->__('Competitor Price'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'reason',
            'select',
            array(
                'label' => Mage::helper('repricer')->__('reason'),
                'title' => Mage::helper('repricer')->__('reason'),
                'name' => 'reason',
                'required' => true,
                'options' => array(
                    '0' => 'no match',
                    '1' => 'active',
                    '2' => 'out of stock',
                    '3' => 'not available',
                    '4' => 'rong match'
                ),
            )
        );
        $data = $model->getData();
        $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id', $data['product_id'])->getFirstItem();
        $competitor = Mage::getModel('ccc_repricer/competitors')->getCollection()->addFieldToFilter('competitor_id', $data['competitor_id'])->getFirstItem();

        $mainData = array('repricer_id' => $data['repricer_id'], 'product_id' => $data['product_id'], 'product_name' => $product->getName(), 'competitor_id' => $data['competitor_id'], 'competitor_name' => $competitor->getName(), 'competitor_url' => $data['competitor_url'], 'competitor_price' => $data['competitor_price'], 'competitor_sku' => $data['competitor_sku']);
        $form->setValues($model->getData());
        $form->setValues($mainData);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

?>