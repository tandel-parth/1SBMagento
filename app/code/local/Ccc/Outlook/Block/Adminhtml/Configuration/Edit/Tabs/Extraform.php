<?php

class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tabs_Extraform extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('outlook/form.phtml');
    }
    public function getPrimaryId()
    {
        $model = Mage::registry('ccc_outlook_configuration');
        $events = Mage::getModel('ccc_outlook/event')->getCollection()
            ->addFieldToFilter('config_id', $model->getId())
            ->getData();
        $count = 0;
        $groupId = 0;
        $result = [];
        foreach ($events as $event) {
            if (!$count) {
                $groupId = $event['group_id'];
            }
            if ($groupId !== $event['group_id']) {
                $groupId = $event['group_id'];
            }
            $result[$groupId][] = $event;
            $count++;
        }
        return $result;
    }
}
