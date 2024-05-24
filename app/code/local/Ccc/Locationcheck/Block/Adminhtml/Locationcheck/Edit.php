<?php 

class Ccc_Locationcheck_Block_Adminhtml_Locationcheck_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_locationcheck';
        $this->_blockGroup = 'locationcheck';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('locationcheck')->__('Save Location'));
        $this->_updateButton('delete', 'label', Mage::helper('locationcheck')->__('Delete Location'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('locationcheck_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'locationcheck_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'locationcheck_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
}

?>