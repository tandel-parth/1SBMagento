<?php
class Ccc_Filetransfer_Model_Observer
{
    public function readfile()
    {
        try {
            $configurationCollection = Mage::getModel('ccc_filetransfer/configuration')
                ->getCollection();
            foreach ($configurationCollection as $configuration) {
                $configuration->readfile();
            }
        } catch (Exception $e) {
            Mage::log('Error reading emails: ' . $e->getMessage(), null, 'filetransfer.log');
        }
    }
}
