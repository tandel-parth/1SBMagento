<?php

class Ccc_Repricer_Adminhtml_CompetitorsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog')
            ->_addBreadcrumb(Mage::helper('repricer')->__('REPRICER'), Mage::helper('repricer')->__('REPRICER'))
            ->_addBreadcrumb(Mage::helper('repricer')->__('Manage Repricer'), Mage::helper('repricer')->__('Manage Repricer'));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Competitors"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_title($this->__('Manage Competitors'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('competitor_id');
        $model = Mage::getModel('ccc_repricer/competitors');
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('repriser')->__('This page no longer exists.')
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

        Mage::register('ccc_repricer_competitor', $model);
        // 5. Build edit form
        $this->_initAction()
            // 4. Register model to use later in blocks
            ->_addBreadcrumb(
                $id ? Mage::helper('repricer')->__('Edit Page')
                : Mage::helper('repricer')->__('New Page'),
                $id ? Mage::helper('repricer')->__('Edit Page')
                : Mage::helper('repricer')->__('New Page')
            );
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $data = $this->_filterPostData($data);
            $model = Mage::getModel('ccc_repricer/competitors');

            if ($id = $this->getRequest()->getParam('competitor_id')) {
                $model->load($id);
            }
            $model->setData($data);

            Mage::dispatchEvent('repricer_competitor_form_prepare_save', array('repricer_competitors' => $model, 'request' => $this->getRequest()));

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('competitor_id' => $model->getId(), '_current' => true));
                return;
            }
            
            // try to save it
            try {
                // save the data
                $model->save();
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('repricer')->__('The page has been saved.')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('competitor_id' => $model->getId(), '_current' => true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
                
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('repricer')->__('An error occurred while saving the page.')
                );
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('competitor_id' => $this->getRequest()->getParam('competitor_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('competitor_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('ccc_repricer/competitors');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('repricer')->__('The page has been deleted.')
                );
                // go to grid
                Mage::dispatchEvent('adminhtml_comprtitor_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_comprtitor_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('competitors_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('repricer')->__('Unable to find a page to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $competitoresIds = $this->getRequest()->getParam('competitor_id');
        if (!is_array($competitoresIds)) {
            $this->_getSession()->addError($this->__('Please select competitors(s).'));
        } else {
            if (!empty($competitoresIds)) {
                try {
                    foreach ($competitoresIds as $competitoresId) {
                        $competitores = Mage::getSingleton('ccc_repricer/competitors')->load($competitoresId);
                        $competitores->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($competitoresIds))
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
        $competitoresIds = $this->getRequest()->getParam('competitor_id');
        $status = $this->getRequest()->getParam('status');
        if (!is_array($competitoresIds)) {
            $competitoresIds = array($competitoresIds);
        }

        try {
            foreach ($competitoresIds as $competitoresId) {
                $competitores = Mage::getModel('ccc_repricer/competitors')->load($competitoresId);
                // Check if the status is different than the one being set
                if ($competitores->getStatus() != $status) {
                    $competitores->setStatus($status)->save();
                }
            }
            // Use appropriate success message based on the status changed
            if ($status == 1) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been enabled.', count($competitoresIds))
                );
            } else {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been disabled.', count($competitoresIds))
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
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
}

?>