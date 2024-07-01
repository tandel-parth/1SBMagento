<?php 

class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_mostselling';
        $this->_blockGroup = 'mostsellingrelateditems';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('mostsellingrelateditems')->__('Save Product'));
        $this->_updateButton('delete', 'label', Mage::helper('mostsellingrelateditems')->__('Delete Product'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('mostsellingrelateditems_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'mostsellingrelateditems_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'mostsellingrelateditems_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
}

?>