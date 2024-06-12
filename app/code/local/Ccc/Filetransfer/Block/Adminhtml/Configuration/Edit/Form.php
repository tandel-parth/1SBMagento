<?php
class Ccc_Filetransfer_Block_Adminhtml_Configuration_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {   
        parent::__construct();
        $this->setId('configuration_form');
        $this->setTitle(Mage::helper('filetransfer')->__('Manage User Details'));
        $this->setData("user_save",$this->getUrl('*/*/save'));
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    protected function _prepareForm()
    {
        $model = Mage::registry('ccc_filetransfer_configuration');
        // print_r($model);
        $isEdit = ($model && $model->getId());
    
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('user_save'), 'method' => 'post', 'enctype'=>"multipart/form-data")
        );
    
        $form->setHtmlIdPrefix('block_');
    
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('filetransfer')->__('General Information'), 'class' => 'fieldset-wide'));
    
        if ($isEdit && $model->getId()) {
            $fieldset->addField(
                'configuration_id',
                'hidden',
                array(
                    'name' => 'configuration_id',
                )
            );
        }
        $fieldset->addField(
            'username',
            'text',
            array(
                'name' => 'username',
                'label' => Mage::helper('filetransfer')->__('User Name'),
                'title' => Mage::helper('filetransfer')->__('User Name'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'password',
            'text',
            array(
                'name' => 'password',
                'label' => Mage::helper('filetransfer')->__('User Password'),
                'title' => Mage::helper('filetransfer')->__('User Password'),
                'required' => true,
            )
        );
    
        $fieldset->addField(
            'host',
            'text',
            array(
                'label' => Mage::helper('filetransfer')->__('Host'),
                'title' => Mage::helper('filetransfer')->__('Host'),
                'name' => 'host',
                'required' => true,
            )
        );    
        $fieldset->addField(
            'port',
            'text',
            array(
                'label' => Mage::helper('filetransfer')->__('Port'),
                'title' => Mage::helper('filetransfer')->__('Port'),
                'name' => 'port',
                'required' => true,
            )
        );
    
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
    
        return parent::_prepareForm();
    }
    
}
?>