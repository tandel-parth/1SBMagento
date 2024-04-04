<?php
class Ccc_Banner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        // echo 12;
        $this->loadLayout();
        $this->_title($this->__("Manage Banners"));
        $this->renderLayout();
    }
}
