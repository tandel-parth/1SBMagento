<?php 

class Ccc_Productseller_Block_Adminhtml_Productseller_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_productseller';
        $this->_blockGroup = 'productseller';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('productseller')->__('Save Seller'));
        $this->_updateButton('delete', 'label', Mage::helper('productseller')->__('Delete Seller'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('productseller_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'productseller_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'productseller_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
}

?>