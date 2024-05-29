<?php

class Ccc_Locationcheck_Adminhtml_LoadReportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales')
            ->_addBreadcrumb(Mage::helper('locationcheck')->__('LOCATIONCHECK'), Mage::helper('locationcheck')->__('LOCATIONCHECK'))
            ->_addBreadcrumb(Mage::helper('locationcheck')->__('Manage Report Grid'), Mage::helper('locationcheck')->__('Manage Report Grid'));
        return $this;
    }
    protected function _isAllowed()
    {
        $aclResource = '';
        $action = strtolower($this->getRequest()->getActionName());
        // Mage::log($action,null,"action.log");
        switch ($action) {
            case 'index':
                $aclResource = 'sales/locationcheck/report/actions/index';
                break;
            case 'getdata':
                $aclResource = 'sales/locationcheck/report/actions/grid';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Report Grid"));
        $this->_initAction();
        $this->renderLayout();
        Mage::dispatchEvent('location_report_event', []);
    }
    public function getDataAction()
    {
        $locationCheck =  $this->getRequest()->getParam('is_location_checked');
        $productExcluded =  $this->getRequest()->getParam('product_excluded_location_check');
        $collection = Mage::getModel('sales/order')->getCollection();
        // Mage::log($collection,null,'mage.log', true);
        // $shippingAddress = $collection->getShippingAddress();
        // die;
        if ($productExcluded !== 'khali') {
            $collection->addFieldToFilter('product_excluded_location_check', $productExcluded);
        }
        $data = $collection->addFieldToFilter('is_location_checked', $locationCheck)->getData();
        $this->getResponse()->setBody(json_encode($data));
    }
}
