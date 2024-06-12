<?php
class Ccc_Filetransfer_Block_Adminhtml_Configuration_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'configuration_id';
        $this->_controller = 'adminhtml_configuration';
        $this->_blockGroup = 'filetransfer';
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('filetransfer')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('filetransfer')->__('Delete User'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('filetransfer_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'filetransfer_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'filetransfer_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    public function getHeaderText(){
        if(Mage::registry('ccc_filetransfer_configuration')->getId()){
            return Mage::helper('jethalal')->__('Edit User Details');
        }else{
            return Mage::helper('jethalal')->__('New User Details');
        }
    }
}
