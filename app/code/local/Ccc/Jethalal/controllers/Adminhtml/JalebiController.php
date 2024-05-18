<?php

class Ccc_Jethalal_Adminhtml_JalebiController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ccc_jethalal')
            ->_addBreadcrumb(Mage::helper('jethalal')->__('JETHALAL'), Mage::helper('jethalal')->__('REPRICER'))
            ->_addBreadcrumb(Mage::helper('jethalal')->__('Manage Jalebi'), Mage::helper('jethalal')->__('Manage jethalal'));
        return $this;
    }
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'new':
                $aclResource = 'ccc_jethalal/jalebi/actions/new';
                break;
            case 'edit':
                $aclResource = 'ccc_jethalal/jalebi/actions/edit';
                break;
            case 'save':
                $aclResource = 'ccc_jethalal/jalebi/actions/save';
                break;
            case 'delete':
                $aclResource = 'ccc_jethalal/jalebi/actions/delete';
                break;
            default:
                $aclResource = 'ccc_jethalal/jalebi/actions/index';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Jalebi"));
        $this->_initAction();
        $this->renderLayout();
        Mage::dispatchEvent('jethalal_event', ['Jalebi','Fafda']);
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_title($this->__('Manage Jalebi'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('jalebi_id');
        $model = Mage::getModel('ccc_jethalal/jalebi');
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('jethalal')->__('This page no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_title($model->getId() ? $this->__('Edit Page') : $this->__('New Page'));
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('ccc_jethalal_jalebi', $model);
        // 5. Build edit form
        $this->_initAction()
            // 4. Register model to use later in blocks
            ->_addBreadcrumb(
                $id ? Mage::helper('jethalal')->__('Edit Page')
                    : Mage::helper('jethalal')->__('New Page'),
                $id ? Mage::helper('jethalal')->__('Edit Page')
                    : Mage::helper('jethalal')->__('New Page')
            );
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $jalebiId = $this->getRequest()->getPost('jalebi_id');
            $jalebiType = $this->getRequest()->getPost('jalebi_type');
            $status = $this->getRequest()->getPost('status');
            Mage::log($status,null,"save.log");
            $jethalal = Mage::getModel('ccc_jethalal/jalebi');

            if ($jalebiId) {
                $jethalal->addData(['jalebi_id' => $jalebiId]);
                $jethalal->addData(['jalebi_type' => $jalebiType]);
                $jethalal->addData(['status' => $status]);
                $jethalal->save();
            }
            $response = array(
                'success' => true,
                'message' => 'Data saved successfully'
            );
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($response));
        }
    }
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
        return $data;
    }
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            /** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            if (
                !empty($data['custom_layout_update_xml'])
                && !$validatorCustomLayout->isValid($data['custom_layout_update_xml'])
            ) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('jalebi_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('ccc_jethalal/jalebi');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('jethalal')->__('The page has been deleted.')
                );
                // go to grid
                Mage::dispatchEvent('adminhtml_jalebi_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_jalebi_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('jalebi_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jethalal')->__('Unable to find a page to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $jalebiIds = $this->getRequest()->getParam('jalebi_id');
        if (!is_array($jalebiIds)) {
            $this->_getSession()->addError($this->__('Please select jalebi(s).'));
        } else {
            if (!empty($jalebiIds)) {
                try {
                    foreach ($jalebiIds as $jalebiId) {
                        $jalebi = Mage::getSingleton('ccc_jethalal/jalebi')->load($jalebiId);
                        $jalebi->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($jalebiIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massStatusAction()
    {
        $jalebiIds = $this->getRequest()->getParam('jalebi_id');
        $status = $this->getRequest()->getParam('status');
        if (!is_array($jalebiIds)) {
            $jalebiIds = array($jalebiIds);
        }

        try {
            foreach ($jalebiIds as $jalebiId) {
                $jalebi = Mage::getModel('ccc_jethalal/jalebi')->load($jalebiId);
                // Check if the status is different than the one being set
                if ($jalebi->getStatus() != $status) {
                    $jalebi->setStatus($status)->save();
                }
            }
            // Use appropriate success message based on the status changed
            if ($status == 1) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been enabled.', count($jalebiIds))
                );
            } else {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been disabled.', count($jalebiIds))
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
