<?php

class Ccc_Repricer_Adminhtml_MatchingController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(Mage::helper('repricer')->__('REPRICER'), Mage::helper('repricer')->__('REPRICER'))
            ->_addBreadcrumb(Mage::helper('repricer')->__('Manage Repricer'), Mage::helper('repricer')->__('Manage Repricer'))
        ;
        return $this;
    }
    public function indexAction()
    {

        $this->_title($this->__("Manage repricer"));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $repricerId = $this->getRequest()->getPost('itemId');
            $editedData = $this->getRequest()->getPost('editedData');
            $repricer = Mage::getModel('ccc_repricer/matching');

            if ($repricerId) {
                $repricer->addData(['repricer_id' => $repricerId]);
                foreach ($editedData as $field => $value) {
                    $repricer->addData([$field => $value]);
                }

                // if (!empty($editedData['competitor_sku']) && !empty($editedData['competitor_url']))
                // {
                //     if ($editedData['competitor_price']>0){
                //         if(!empty($editedData['reason']) && !is_null($editedData['reason'])){
                //             $repricer->addData(['reason' => $repricer::CONST_REASON_NO_OUT_OF_STOCK]);
                //         }else{
                //             $repricer->addData(['reason' => $repricer::CONST_REASON_ACTIVE]);
                //         }
                //     } else {
                //         $repricer->addData(['reason' => $repricer::CONST_REASON_NOT_AVAILABLE]);
                //     }
                // } else {
                //     $repricer->addData(['reason' => $repricer::CONST_REASON_NO_MATCH]);
                // }
                switch ($repricer->getReason()) {
                    case $repricer::CONST_REASON_ACTIVE:
                        $this->_reasonCheck($repricer);
                        break;
                    case $repricer::CONST_REASON_NOT_AVAILABLE:
                        $repricer->addData(['competitor_price' => 0]);
                        break;
                    case $repricer::CONST_REASON_NO_OUT_OF_STOCK:
                        break;
                    case $repricer::CONST_REASON_NO_MATCH:
                        $this->_reasonCheck($repricer);
                        break;
                    case $repricer::CONST_REASON_NO_WRONG_MATCH:
                        $collection = Mage::getModel('ccc_repricer/matching')->load($repricerId);
                        $url = $repricer->getCompetitorUrl();
                        $sku = $repricer->getCompetitorSku();
                        if (!empty($url) && !empty($sku) && ($collection->getReason() == $repricer::CONST_REASON_NO_WRONG_MATCH)) {
                            if (($collection->getCompetitorUrl() != $repricer->getCompetitorUrl()) || ($collection->getCompetitorSku() != $repricer->getCompetitorSku())) {
                                $repricer->addData(['competitor_price' => 0.0]);
                                $repricer->addData(['reason' => $repricer::CONST_REASON_NOT_AVAILABLE]);
                            }
                        }
                        break;
                }

                $repricer->save();
            }

            $response = array(
                'success' => true,
                'message' => 'Data saved successfully'
            );
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($collection->getReason()));
        }
    }
    protected function _reasonCheck($model)
    {
        $url = $model->getCompetitorUrl();
        $sku = $model->getCompetitorSku();
        $price = $model->getCompetitorPrice();

        if (!empty($url) && !empty($sku)) {
            if (!empty($price)) {
                $model->addData(['reason' => $model::CONST_REASON_ACTIVE]);
            } else {
                $model->addData(['reason' => $model::CONST_REASON_NOT_AVAILABLE]);
            }
        } else {
            $model->addData(['reason' => $model::CONST_REASON_NO_MATCH]);
        }
    }
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('repricer/adminhtml_matching/grid')->getGridHtml()
        );
    }

}

?>