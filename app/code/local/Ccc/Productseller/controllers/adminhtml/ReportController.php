<?php

class Ccc_Productseller_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer')
            ->_addBreadcrumb(Mage::helper('productseller')->__('PRODUCTSELLER'), Mage::helper('productseller')->__('PRODUCTSELLER'))
            ->_addBreadcrumb(Mage::helper('productseller')->__('Manage Seller Report'), Mage::helper('productseller')->__('Manage Seller Report'));
        return $this;
    }
    protected function _isAllowed()
    {
        $aclResource ='';
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'index':
                $aclResource = 'ccc_productseller/report/actions/index';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Seller Report"));
        $this->_initAction();
        $this->renderLayout();
        Mage::dispatchEvent('report_event', []);
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productseller/adminhtml_report_grid')->toHtml()
        );
        // Mage::dispatchEvent('hide_massaction', []);
    }
    public function massSellerAction()
    {
        $Ids = $this->getRequest()->getParam('entity_id');
        $active = $this->getRequest()->getParam('seller_id');
        if (!is_array($Ids)) {
            $Ids = array($Ids);
        }

        try {
            foreach ($Ids as $Id) {
                $seller = Mage::getModel('catalog/product')->load($Id);
                // Check if the status is different than the one being set
                if ($seller->getEntityId() != $active) {
                    $seller->addData(["seller_id" => $active]);
                    $seller->save();
                }
            }
            // Use appropriate success message based on the status changed
            if ($active) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been save.', count($Ids))
                );
            } 
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
