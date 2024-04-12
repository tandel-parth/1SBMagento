<?php  

class Ccc_Repricer_Adminhtml_ManagecompetitorsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog');
            // ->_addBreadcrumb(Mage::helper('repriser')->__('REPRICER'), Mage::helper('repriser')->__('REPRICER'))
            // ->_addBreadcrumb(Mage::helper('repriser')->__('Manage Repriser'), Mage::helper('repriser')->__('Manage Repriser'))
        ;
        return $this;
    }
    public function indexAction(){
       
        $this->_title($this->__("Manage repricer"));
        $this->_initAction();
        echo $this->getFullActionName();
        $this->renderLayout();
    }
}

?>