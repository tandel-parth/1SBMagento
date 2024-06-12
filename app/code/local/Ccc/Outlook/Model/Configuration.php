<?php
class Ccc_Outlook_Model_Configuration extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/configuration');
    }
    public function fetchMail()
    {
        $api = Mage::getModel('ccc_outlook/api');
        $api->setConfigModel($this);
        $emails = $api->getEmails();
        foreach ($emails as $email) {
            $emailModel = Mage::getModel('ccc_outlook/email');
            $emailModel->setConfigModel($this);
            $emailModel->setRowData($email);
            $emailModel->save();
            if ($email['has_attachments']) {
                $emailModel->fetchAndSave($email);
            }
            $this->displatchEvent($emailModel);
        }
        // $this->setLastReadedEmail($this->formatDates($emails[0]['createdDateTime']))->save();
    }
    public function formatDates($dateString)
    {
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }
    public function displatchEvent($emailModel)
    {
        $events = Mage::getModel('ccc_outlook/event')->getCollection()
            ->addFieldToFilter('config_id', $this->getId());
        $groups = [];
        if ($events) {
            foreach ($events as $event) {
                $groupId = $event->getGroupId();
                if (!isset($groups[$groupId])) {
                    $groups[$groupId] = [];
                }
                $groups[$groupId][] = $event;
            }
        }
        $data = $emailModel->getData();
        foreach ($groups as $group) {
            if ($group) {
                $flag = true;
                foreach ($group as $event) {
                    $conditionOn = $event->getData('condition_on');
                    $eventName = $event->getData('event');
                    $operator = $event->getData('operator');
                    $value = $event->getData('value');
                    switch ($operator) {
                        case '=':
                            $result = ($data[$conditionOn] == $value);
                            $flag = $result && $flag;
                            break;
                        case 'like':
                            $result = (strpos($data[$conditionOn], $value) !== false);
                            $flag = $result && $flag;
                            break;
                        case '%like%':
                            $result = (stripos($data[$conditionOn], $value) !== false);
                            $flag = $result && $flag;
                            break;
                        default:
                            $result = false;
                            $flag = $result && $flag;
                    }
                }
                if ($flag) {
                    $this->triggerEvent($eventName, $emailModel);
                }
            }
        }
    }

    private function triggerEvent($eventName, $email)
    {
        Mage::dispatchEvent($eventName, ['email' => $email]);
    }
}
