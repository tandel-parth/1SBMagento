<?php
class Ccc_Ticketsystem_Block_Adminhtml_Filter extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('ticketsystem/filter.phtml');
    }
    public function adminUsers()
    {
        return Mage::getModel('admin/user')->getCollection()->getData();
    }
    public function statusData()
    {
        return Mage::getModel('ccc_ticketsystem/status')->getCollection()->getData();
    }
}
