<?php

class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pages_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Configuration Form');
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('tab_first', array(
            'label'   => 'General Information',
            'title'   => 'General Information',
            'content' => $this->getLayout()->createBlock('outlook/adminhtml_configuration_edit_tabs_generalform')->toHtml(),
        ));

        $this->addTab('tab_second', array(
            'label'   => 'Extra Information',
            'title'   => 'Extra Information',
            'content' => $this->getLayout()->createBlock('outlook/adminhtml_configuration_edit_tabs_extraform')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
