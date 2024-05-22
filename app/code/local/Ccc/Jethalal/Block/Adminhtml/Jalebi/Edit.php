<?php 

class Ccc_Jethalal_Block_Adminhtml_Jalebi_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'jalebi_id';
        $this->_controller = 'adminhtml_jalebi';
        $this->_blockGroup = 'jethalal';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('jethalal')->__('Save Jalebi'));
        $this->_updateButton('delete', 'label', Mage::helper('jethalal')->__('Delete Jalebi'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('jalebi_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'jalebi_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'jalebi_content');
            }
        }s
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
    public function getHeaderText(){
        if(Mage::registry('ccc_jethalal_jalebi')->getJalebiId()){
            return Mage::helper('jethalal')->__('Edit Jalebi');
        }else{
            return Mage::helper('jethalal')->__('New Jalebi');
        }
    }
}

?>