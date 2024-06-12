<?php
class Ccc_Outlook_Model_Email extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/email');
    }
    protected $_configObj = null;
    public function setConfigModel($obj)
    {
        $this->_configObj = $obj;
    }
    public function getConfigModel()
    {
        return $this->_configObj;
    }
    public function setRowData($mail)
    {
        $data = [
            'subject' => $mail['subject'],
            'from_email' => $mail['from'],
            'to_email' => $mail['to'],
            'body' => $mail['body'],
            'configration_id' => $this->getConfigModel()->getId(),
        ];
        $this->setData($data);
    }
    public function fetchAndSave($mail)
    {
        $apiModel = Mage::getModel('ccc_outlook/api');
        $apiModel->setConfigModel($this->getConfigModel());
        $accessToken = $apiModel->readTokenFromFile();
        $params = array(
            'accesstoken' => $accessToken,
            'message_id' => $mail['id'],
            'email_id' => $this->getConfigModel()->getEmail()
        );
        $attachments = $apiModel
        ->downloadAttachments($params);
        $id= $this->getId();
        foreach ($attachments as $attachment) {
            Mage::getModel('ccc_outlook/attachment')
                ->saveAttachments($attachment, $id);
        }
        return $id;
    }
}
