<?php

class Ccc_Ticketsystem_Adminhtml_TicketsystemController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ccc_ticketsystem')
            ->_addBreadcrumb(Mage::helper('ticketsystem')->__('Manage Ticket System'), Mage::helper('ticketsystem')->__('Manage Ticket System'));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Manage Ticket System"));
        $this->_initAction();
        $this->renderLayout();
    }
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('data');

        if ($data) {
            // Sanitize input data (optional, depending on your security requirements)
            $data['descreption'] = htmlspecialchars($data['descreption'], ENT_QUOTES);

            $ticket = Mage::getModel('ccc_ticketsystem/ticketsystem')->setData($data);

            try {
                $ticket->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ticketsystem')->__('Ticket was successfully saved'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            $this->_redirect('*/*/');
        }
    }
    public function viewAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }
    public function saveFieldAction()
    {
        $response = array();
        $ticketId = $this->getRequest()->getParam('id');
        $FieldName = $this->getRequest()->getParam('field');
        $FieldValue = $this->getRequest()->getParam('value');
        $data = [];
        $data['ticket_id'] = $ticketId;
        $data[$FieldName] = $FieldValue;

        if ($data) {
            // Sanitize input data (optional, depending on your security requirements)
            if (array_key_exists('descreption', $data)) {
                $data['descreption'] = htmlspecialchars($data['descreption'], ENT_QUOTES);
            }

            $ticket = Mage::getModel('ccc_ticketsystem/ticketsystem')->setData($data);

            try {
                $ticket->save();
                $response['success'] = true;
                $response['message'] = 'Ticket saved successfully';
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($response));
        }
    }
    public function saveCommentAction()
    {
        $data = $this->getRequest()->getParam('datasave');
        if ($data) {
            // Sanitize input data (optional, depending on your security requirements)
            if (array_key_exists('description', $data)) {
                $data['description'] = htmlspecialchars($data['description'], ENT_QUOTES);
            }

            $comment = Mage::getModel('ccc_ticketsystem/comment')->setData($data);

            try {
                $comment->save();
                $response['success'] = true;
                $response['message'] = 'Ticket saved successfully';
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($response));
        }
    }
    public function commentAction(){
        $response = Mage::getModel('ccc_ticketsystem/comment')
        ->getCollection()
        ->setOrder('comment_id', 'DESC')
        ->getData();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    public function saveFilterAction(){
        $data = $this->getRequest()->getPost('data');
        echo "<pre>";
        print_r($data);
    }
}
