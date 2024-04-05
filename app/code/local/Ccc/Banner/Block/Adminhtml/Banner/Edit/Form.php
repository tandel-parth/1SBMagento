<?php
class Ccc_Banner_Block_Adminhtml_Banner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {   
        parent::__construct();
        $this->setId('banner_form');
        $this->setTitle(Mage::helper('banner')->__('Manage Banners'));
        $this->setData("banner_save",$this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_banner');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData("banner_save"), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('banner_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('banner')->__('Banner Form'), 'class' => 'fieldset-wide'));

        if ($model->getBannerId()) {
            $fieldset->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
        }

        $fieldset->addField('banner_name', 'text', array(
            'name'      => 'banner_name',
            'label'     => Mage::helper('banner')->__('Banner Name'),
            'title'     => Mage::helper('banner')->__('Banner Name'),
            'required'  => true,
        ));

        $fieldset->addField('banner_image', 'file', array(
            'name'      => 'banner_image',
            'label'     => Mage::helper('banner')->__('Banner Image'),
            'title'     => Mage::helper('banner')->__('Banner Image'),
            'required'  => true,
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('banner')->__('Status'),
            'title'     => Mage::helper('banner')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('banner')->__('Enabled'),
                '0' => Mage::helper('banner')->__('Disabled'),
            ),
        ));

        $fieldset->addField('show_on', 'select', array(
            'label'     => Mage::helper('banner')->__('Show On'),
            'title'     => Mage::helper('banner')->__('Show On'),
            'name'      => 'show_on',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('banner')->__('1'),
                '0' => Mage::helper('banner')->__('0'),
            ),
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
?>