<?php
class Ccc_Outlook_Model_Observer
{
    public function getEmails(){
        try {
            $configurationCollection = Mage::getModel('ccc_outlook/configuration')
                ->getCollection()
                ->addFieldToFilter('is_active', 1);
                // print_r($configurationCollection->getData());
                // die("done");
                foreach ($configurationCollection as $configuration){
                    $configuration->fetchMail();
                    echo $configuration->getId();
                }
        } catch (Exception $e) {
            Mage::log('Error reading emails: ' . $e->getMessage(), null, 'outlook_emails.log');
        }
    }
    public function checkEvent(){
        echo 123;
    }
}