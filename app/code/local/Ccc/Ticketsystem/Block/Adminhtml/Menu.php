<?php
class Ccc_Ticketsystem_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ticketsystem/page/menu.phtml');
    }
    public function adminUsers(){
        return Mage::getModel('admin/user')->getCollection()->getData();
    }
    public function statusData(){
        return Mage::getModel('ccc_ticketsystem/status')->getCollection()->getData();
    }
}