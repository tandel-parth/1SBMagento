<?php 
class Ccc_Outlook_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        $code = $this->getRequest()->getParam('code');
        $id=$this->getRequest()->getParam('id');
        $apiModel=Mage::getModel('ccc_outlook/api');
        $configurationModel=Mage::getModel('ccc_outlook/configuration')
        ->load($id);
        $apiModel->setConfigModel($configurationModel);
        $token=$apiModel->getAccessToken($code);
        $apiModel->saveTokenToFile($token);
    }
}
