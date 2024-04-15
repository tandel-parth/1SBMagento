<?php 

class Ccc_Repricer_Block_Adminhtml_Matching_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'repricer_id';
        $this->_controller = 'adminhtml_matching';
        $this->_blockGroup = 'repricer';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('repricer')->__('Save Matching'));
        $this->_updateButton('delete', 'label', Mage::helper('repricer')->__('Delete Matching'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('matching_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'matching_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'matching_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
}

?>