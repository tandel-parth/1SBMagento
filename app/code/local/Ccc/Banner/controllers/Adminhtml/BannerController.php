<?php
class Ccc_Banner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('ccc_banner/banner')
            ->_addBreadcrumb(Mage::helper('banner')->__('BANNER'), Mage::helper('banner')->__('BANNER'))
            ->_addBreadcrumb(Mage::helper('banner')->__('Manage Banner'), Mage::helper('banner')->__('Manage Banner'))
        ;
        return $this;
    }
    public function indexAction()
    {
        // echo 12;
        $this->loadLayout();
        $this->_title($this->__("Manage Banners"));
        $this->renderLayout();
    }
    public function newAction(){
        $this->_forward('edit');
    }
    public function editAction(){
        $this->_title($this->__('BANNER'))
             ->_title($this->__('banner'))
             ->_title($this->__('Manage Banner'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('banner_id');
        $model = Mage::getModel('ccc_banner/banner');
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('banner')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Page'));
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('ccc_banner', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('banner')->__('Edit Page')
                    : Mage::helper('banner')->__('New Page'),
                $id ? Mage::helper('banner')->__('Edit Page')
                    : Mage::helper('banner')->__('New Page'));

        $this->renderLayout();
    }
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            //init model and set data
            $model = Mage::getModel('ccc_banner/banner');

            if ($id = $this->getRequest()->getParam('banner_id')) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::dispatchEvent('banner_form_prepare_save', array('banner' => $model, 'request' => $this->getRequest()));

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('banner_id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('banner')->__('The page has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('banner_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('banner')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
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
            if (!empty($data['custom_layout_update_xml'])
            && !$validatorCustomLayout->isValid($data['custom_layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }
}
