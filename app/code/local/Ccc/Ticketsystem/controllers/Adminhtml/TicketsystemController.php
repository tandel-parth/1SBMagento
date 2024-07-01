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
        $data = json_decode($data, true);
        $adminSession = Mage::getSingleton('admin/session');
        if ($adminSession->isLoggedIn()) {
            $user = $adminSession->getUser();
            $userId = $user->getId();
            $data['user_id'] = $userId;
        }
        if ($data) {
            // Sanitize input data (optional, depending on your security requirements)
            if (array_key_exists('description', $data)) {
                $data['description'] = htmlspecialchars($data['description'], ENT_QUOTES);
            }
            $model = Mage::getModel('ccc_ticketsystem/comment');
            $comment = $model->setData($data);


            try {
                $comment->save();
                $response['data'] = $model->getData();
                $response['url'] = $this->getUrl('*/*/commentdata');
                $response['success'] = true;
                $response['message'] = 'Comment saved successfully';
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($response));
        }
    }

    public function updateStatusAction()
    {
        $commentId = $this->getRequest()->getPost('comment_id');
        $status = $this->getRequest()->getPost('status');
        $response = $this->saveUpdatedStatus($commentId, $status);
        $response['url'] = $this->getUrl('*/*/commentdata');
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }

    private function saveUpdatedStatus($commentId, $status)
    {
        $model = Mage::getModel('ccc_ticketsystem/comment')->load($commentId);

        if (!$model->getId()) {
            return ['success' => false, 'message' => 'Comment not found'];
        }
        $model->addData(['status' => $status])->save();

        if ($status == 'complete') {
            $parentId = $model->getParentId();
            if ($parentId) {
                $allChildrenComplete = $this->areAllChildrenComplete($parentId);
                if ($allChildrenComplete) {
                    $this->saveUpdatedStatus($parentId, $status);
                }
            }
        }

        return [
            'success' => true,
            'message' => 'Status updated successfully',
            'ticketId' => $model->getTicketId()
        ];
    }

    private function areAllChildrenComplete($parentId)
    {
        $children = Mage::getModel('ccc_ticketsystem/comment')
            ->getCollection()
            ->addFieldToFilter('parent_id', $parentId);

        foreach ($children as $child) {
            if ($child->getStatus() != 'complete') {
                return false;
            }
        }
        return true;
    }



    public function commentAction()
    {
        $data = Mage::getModel('ccc_ticketsystem/comment')
            ->getCollection()
            ->setOrder('comment_id', 'DESC')
            ->getData();

        $response['data'] = $data;
        $response['success'] = true;
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    public function saveFilterAction()
    {
        $filterData = $this->getRequest()->getPost('data');
        $filterName = $this->getRequest()->getPost('filter_name');
        $data = array(
            'filter_name' => $filterName,
            'filter_data' => json_encode($filterData)
        );

        if ($data) {

            $filter = Mage::getModel('ccc_ticketsystem/filter')->setData($data);

            try {
                $filter->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ticketsystem')->__('Filter was successfully saved'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }

            $this->_redirect('*/*/');
        }
    }
    public function applyFiltersAction()
    {
        $block = $this->getLayout()->createBlock('ticketsystem/adminhtml_ticketsystem_grid');
        $this->getResponse()->setBody($block->toHtml());
    }
    // Example usage in your controller action
    public function commentdataAction()
    {
        $block = $this->getLayout()->createBlock('ticketsystem/adminhtml_commentdata');
        $this->getResponse()->setBody($block->toHtml());
    }
    public function lockUpdateAction()
    {
        $response = array();
        $commentIds = $this->getRequest()->getPost('comment_id');
        echo $commentIds;
        $commentIds = json_decode($commentIds, true);
        foreach ($commentIds as $commentId) {
            $model = Mage::getModel('ccc_ticketsystem/comment');
            $model->addData(['comment_id' => $commentId]);
            $model->addData(['is_lock' => 1]);
            try {
                $model->save();
                $response['success'] = true;
                $response['message'] = 'Comment saved successfully';
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = $e->getMessage();
            }
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }
}
