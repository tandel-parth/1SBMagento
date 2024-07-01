<?php

class Ccc_Mostsellingrelateditems_Adminhtml_MostsellingreportController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(Mage::helper('mostsellingrelateditems')->__('Manage Most Releted Product Report'), Mage::helper('mostsellingrelateditems')->__('Manage Most Releted Product Report'));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Most Releted Product Report"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function productDataAction()
    {
        $block = $this->getLayout()->createBlock('mostsellingrelateditems/adminhtml_mostselling_productdata');
        $this->getResponse()->setBody($block->toHtml());
    }
    public function showDataAction()
    {
        $block = $this->getLayout()->createBlock('mostsellingrelateditems/adminhtml_mostselling_relatedgrid');
        $this->getResponse()->setBody($block->toHtml());
    }
}
