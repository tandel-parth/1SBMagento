<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('mostsellingrelateditems_form');
        $this->setTitle(Mage::helper('mostsellingrelateditems')->__('Manage Most Selling Product'));
        $this->setData("mostsellingrelateditems_save", $this->getUrl('*/*/save'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('mostsellingrelateditems');
        $isEdit = ($model && $model->getId());

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('mostsellingrelateditems_save'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('mostsellingrelateditems')->__('General Information'), 'class' => 'fieldset-wide'));

        if ($isEdit) {
            $fieldset->addField(
                'id',
                'hidden',
                array(
                    'name' => 'id',
                )
            );
        }
        $productOptions = $this->getProductOptions();

        $fieldset->addField(
            'most_selling_product_id',
            'select',
            array(
                'name' => 'most_selling_product_id',
                'label' => Mage::helper('mostsellingrelateditems')->__('Most Selling Product'),
                'title' => Mage::helper('mostsellingrelateditems')->__('Most Selling Product'),
                'required' => true,
                'values' => $productOptions,
            )
        );

        $fieldset->addField(
            'related_product_id',
            'select',
            array(
                'name' => 'related_product_id',
                'label' => Mage::helper('mostsellingrelateditems')->__('Related Product'),
                'title' => Mage::helper('mostsellingrelateditems')->__('Related Product'),
                'required' => true,
                'values' => $productOptions,
            )
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getProductOptions()
    {
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->setOrder('entity_id', 'DESC');
        $options = array(
            array('label' => '', 'value' => '')
        );
        foreach ($products as $product) {
            $options[] = array(
                'label' => $product->getName(),
                'value' => $product->getId(),
            );
        }

        return $options;
    }
}
