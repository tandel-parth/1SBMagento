<?php
class Ccc_Ticketsystem_Block_Adminhtml_Comment extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('ticketsystem/comment.phtml');
    }
    public function getTicketData()
    {
        $id = $this->getRequest()->getParam('id');
        return Mage::getModel('ccc_ticketsystem/ticketsystem')->load($id);
    }
    public function getStatusArray()
    {
        $data = Mage::getModel('ccc_ticketsystem/status')->getCollection()->getData();
        $status = [];
        foreach ($data as $_data) {
            $status[] = [
                'id' => $_data['status_id'],
                'value' => $_data['label']
            ];
        }
        return $status;
    }
    public function urlAction($id)
    {
        return $this->getUrl('*/*/saveField', array('id' => $id));
    }
    public function getStatusName($statusId)
    {
        $data = Mage::getModel('ccc_ticketsystem/status')->load($statusId);
        return $data->getLabel();
    }
    public function getAdminArray()
    {
        $data = Mage::getModel('admin/user')->getCollection()->getData();
        $admin = [];
        foreach ($data as $_data) {
            $admin[] = [
                'id' => $_data['user_id'],
                'value' => $_data['username']
            ];
        }
        return $admin;
    }
    public function getAdminName($adminId)
    {
        $data = Mage::getModel('admin/user')->load($adminId);
        return $data->getUsername();
    }
}
