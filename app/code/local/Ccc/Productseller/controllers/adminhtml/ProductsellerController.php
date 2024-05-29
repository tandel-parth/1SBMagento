<?php

class Ccc_Productseller_Adminhtml_ProductsellerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer')
            ->_addBreadcrumb(Mage::helper('productseller')->__('PRODUCTSELLER'), Mage::helper('productseller')->__('PRODUCTSELLER'))
            ->_addBreadcrumb(Mage::helper('productseller')->__('Manage Seller Grid'), Mage::helper('productseller')->__('Manage Seller Grid'));
        return $this;
    }
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        $acl = 'customer/manage';
        if (Mage::getSingleton('admin/session')->isAllowed($acl)) {
            switch ($action) {
                case 'new':
                    $aclResource = 'customer/productseller/grid/actions/new';
                    break;
                case 'edit':
                    $aclResource = 'customer/productseller/grid/actions/edit';
                    break;
                case 'save':
                    $aclResource = 'customer/productseller/grid/actions/save';
                    break;
                case 'delete':
                    $aclResource = 'customer/productseller/grid/actions/delete';
                    break;
                default:
                    $aclResource = 'customer/productseller/grid/actions/index';
                    break;
            }
            return Mage::getSingleton('admin/session')->isAllowed($aclResource);
        }
        return Mage::getSingleton('admin/session')->isAllowed($acl);
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Seller Grid"));
        $this->_initAction();
        $this->renderLayout();
        Mage::dispatchEvent('productseller_event', []);
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_title($this->__('Manage Seller'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('Ccc_Productseller/productseller');
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('productseller')->__('This page no longer exists.')
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

        Mage::register('ccc_seller', $model);
        // 5. Build edit form
        $this->_initAction()
            // 4. Register model to use later in blocks
            ->_addBreadcrumb(
                $id ? Mage::helper('productseller')->__('Edit Page')
                    : Mage::helper('productseller')->__('New Page'),
                $id ? Mage::helper('productseller')->__('Edit Page')
                    : Mage::helper('productseller')->__('New Page')
            );
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $data = $this->_filterPostData($data);
            $model = Mage::getModel('Ccc_Productseller/productseller');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }
            $model->setData($data);

            Mage::dispatchEvent('productseller_productseller_form_prepare_save', array('productseller_productseller' => $model, 'request' => $this->getRequest()));

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('productseller')->__('The page has been saved.')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
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
                    Mage::helper('productseller')->__('An error occurred while saving the page.')
                );
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
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
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('Ccc_Productseller/productseller');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('productseller')->__('The page has been deleted.')
                );
                // go to grid
                Mage::dispatchEvent('adminhtml_productseller_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_productseller_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productseller')->__('Unable to find a page to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $Ids = $this->getRequest()->getParam('id');
        if (!is_array($Ids)) {
            $this->_getSession()->addError($this->__('Please select sellers(s).'));
        } else {
            if (!empty($Ids)) {
                try {
                    foreach ($Ids as $Id) {
                        $seller = Mage::getSingleton('Ccc_Productseller/productseller')->load($Id);
                        $seller->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($Ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massActiveAction()
    {
        $Ids = $this->getRequest()->getParam('id');
        $active = $this->getRequest()->getParam('is_active');
        if (!is_array($Ids)) {
            $Ids = array($Ids);
        }

        try {
            foreach ($Ids as $Id) {
                $seller = Mage::getModel('Ccc_Productseller/productseller')->load($Id);
                // Check if the status is different than the one being set
                if ($seller->getIsActive() != $active) {
                    $seller->addData(["is_active" => $active]);
                    $seller->save();
                }
            }
            // Use appropriate success message based on the status changed
            if ($active == 1) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been Active.', count($Ids))
                );
            } else {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been DeActive.', count($Ids))
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }
}
