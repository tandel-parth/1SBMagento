<?php 

class Ccc_Repricer_Block_Adminhtml_Competitors_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'competitor_id';
        $this->_controller = 'adminhtml_competitors';
        $this->_blockGroup = 'repricer';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('repricer')->__('Save Competitor'));
        $this->_updateButton('delete', 'label', Mage::helper('repricer')->__('Delete Competitor'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('competitor_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'competitor_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'competitor_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
}

?>