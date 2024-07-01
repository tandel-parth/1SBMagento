<?php

class Ccc_Mostsellingrelateditems_Adminhtml_MostsellingController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(Mage::helper('mostsellingrelateditems')->__('Manage Most Releted Product Grid'), Mage::helper('mostsellingrelateditems')->__('Manage Most Releted Product Grid'));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Most Releted Product Grid"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function viewAction(){
        $this->_title($this->__("Manage Most Releted Product View"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_title($this->__('Manage Most Selleing Product'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('mostsellingrelateditems')->__('This page no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_title($model->getId() ? $this->__('Edit Page') : $this->__('New Page'));
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('mostsellingrelateditems', $model);
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('mostsellingrelateditems')->__('Edit Page')
                    : Mage::helper('mostsellingrelateditems')->__('New Page'),
                $id ? Mage::helper('mostsellingrelateditems')->__('Edit Page')
                    : Mage::helper('mostsellingrelateditems')->__('New Page')
            );
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems');
            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mostsellingrelateditems')->__('The Product has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('mostsellingrelateditems')->__('An error occurred while saving the page.')
                );
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                date_default_timezone_set('Asia/Kolkata');
                $model = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems');
                $model->load($id);
                $model->addData(['is_deleted' => 1]);
                $model->addData(['deleted_at' => date('Y-m-d H:i:s')]);
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('mostsellingrelateditems')->__('The Product has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mostsellingrelateditems')->__('Unable to find a page to delete.'));
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $Ids = $this->getRequest()->getParam('id');
        $delete = $this->getRequest()->getParam('is_deleted');
        if (!is_array($Ids)) {
            $Ids = array($Ids);
        }
        try {
            date_default_timezone_set('Asia/Kolkata');
            foreach ($Ids as $Id) {
                $product = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')->load($Id);
                if ($product->getIsActive() != $delete) {
                    $product->setIsDeleted($delete);
                    if ($delete == 2) {
                        $product->setDeletedAt();
                    }else{
                        $product->setDeletedAt(date('Y-m-d H:i:s'));
                    }
                    $product->save();
                }
            }
            if ($delete == 1) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been Deleted.', count($Ids))
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mostsellingrelateditems/adminhtml_mostselling_grid')->toHtml()
        );
    }
}
