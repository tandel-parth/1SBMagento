<?php 

class Ccc_Outlook_Block_Adminhtml_Configuration_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'configration_id';
        $this->_controller = 'adminhtml_configuration';
        $this->_blockGroup = 'outlook';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('outlook')->__('Save Configuration'));
        $this->_updateButton('delete', 'label', Mage::helper('outlook')->__('Delete Configuration'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
        $objId = $this->getRequest()->getParam($this->_objectId);
        if(!empty($objId)){
            $this->_addButton('login', array(
                'label'     => Mage::helper('adminhtml')->__('Login'),
                'onclick'   => 'setLocation(\'' . $this->getLoginUrl($objId) . '\')',
                'class'     => 'login',
            ), -1);
        }
        
        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('configuration_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'configuration_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'configuration_content');
            }
        }
        
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
        ";
    }
    public function getHeaderText(){
        if(Mage::registry('ccc_outlook_configuration')->getJalebiId()){
            return Mage::helper('outlook')->__('Edit Configuration');
        }else{
            return Mage::helper('outlook')->__('New Configuration');
        }
    }
    public function getLoginUrl($objId)
    {
        return $this->getUrl('*/*/login', array('configration_id'=>$objId,
            Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
        ));
    }
}

?>