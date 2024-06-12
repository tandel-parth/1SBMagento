<?php

class Ccc_Outlook_Adminhtml_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ccc_outlook')
            ->_addBreadcrumb(Mage::helper('outlook')->__('Manage Configration'), Mage::helper('outlook')->__('Manage Configration'));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Configration"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_title($this->__('Manage Configuration'));
        $id = $this->getRequest()->getParam('configration_id');
        $model = Mage::getModel('ccc_outlook/configuration');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('outlook')->__('This page no longer exists.')
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

        Mage::register('ccc_outlook_configuration', $model);

        $this->_initAction()

            ->_addBreadcrumb(
                $id ? Mage::helper('outlook')->__('Edit Page')
                    : Mage::helper('outlook')->__('New Page'),
                $id ? Mage::helper('outlook')->__('Edit Page')
                    : Mage::helper('outlook')->__('New Page')
            );
        $this->renderLayout();
    }
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            $data = $this->getRequest()->getPost('cofiguration');

            $model = Mage::getModel('ccc_outlook/configuration');

            if ($id = $this->getRequest()->getParam('configration_id')) {
                $model->load($id);
            }
            $model->setData($data);

            Mage::dispatchEvent('outlook_configuration_form_prepare_save', array('outlook_configuration' => $model, 'request' => $this->getRequest()));

            try {
                $model->save();
                $configurationId = $model->getId();
                $event = $this->getRequest()->getPost('events');

                $this->saveEvent($event, $configurationId);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('outlook')->__('The page has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('configration_id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('outlook')->__('An error occurred while saving the page.')
                );
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('configration_id' => $this->getRequest()->getParam('cofiguration[configration_id]')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function saveEvent($events, $id)
    {
        $model = Mage::getModel('ccc_outlook/event');
        if (empty($events)) {
            $model->getCollection()
                ->addFieldToFilter('config_id', $id)
                ->walk('delete');
        } else {
            $existingEventId = $model->getCollection()
                ->addFieldToFilter('config_id', $id)->getColumnValues('event_id');
            $eventEventIds = [];
            foreach ($events as $event) {
                foreach ($event['rows'] as $row) {
                    if (!empty($row['event_id'])) {
                        $eventEventIds[] = $row['event_id'];
                    }
                }
            }

            $eventIdsToDelete = array_diff($existingEventId, array_unique($eventEventIds));

            if (!empty($eventIdsToDelete)) {
                $model->getCollection()
                    ->addFieldToFilter('config_id', $id)
                    ->addFieldToFilter('event_id', ['in' => $eventIdsToDelete])
                    ->walk('delete');
            }
            $data = array();
            foreach ($events as $event) {
                foreach ($event['rows'] as $row) {
                    $row['event'] = $event['event'];
                    $row['config_id'] = $id;
                    if (empty($row['event_id'])) {
                        unset($row['event_id']);
                    }
                    $data[] = $row;
                }
            }

            foreach ($data as $row) {
                $model->setData($row);
                $model->save();
            }
        }
    }
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('configration_id')) {
            $title = "";
            try {
                $model = Mage::getModel('ccc_outlook/configuration');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('outlook')->__('The page has been deleted.')
                );
                Mage::dispatchEvent('adminhtml_configuration_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_configuration_on_delete', array('title' => $title, 'status' => 'fail'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('configration_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('outlook')->__('Unable to find a page to delete.'));
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $configurationIds = $this->getRequest()->getParam('configration_id');
        if (!is_array($configurationIds)) {
            $this->_getSession()->addError($this->__('Please select configuration(s).'));
        } else {
            if (!empty($configurationIds)) {
                try {
                    foreach ($configurationIds as $configurationId) {
                        $configuration = Mage::getSingleton('ccc_outlook/configuration')->load($configurationId);
                        $configuration->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($configurationIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
    public function loginAction()
    {
        $id = $this->getRequest()->getParam('configration_id');
        $configurationModel = Mage::getModel('ccc_outlook/configuration')->load($id);
        $apiModel = Mage::getModel('ccc_outlook/api');
        $apiModel->setConfigModel($configurationModel);
        $authorizationUrl = $apiModel->getAuthorizationUrl();
        $this->_redirectUrl($authorizationUrl);
    }
}
