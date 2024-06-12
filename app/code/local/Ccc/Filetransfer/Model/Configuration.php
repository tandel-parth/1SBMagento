<?php
class Ccc_Filetransfer_Model_Configuration extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_filetransfer/configuration');
    }
    public function readFile(){
        $ftp = Mage::getModel('ccc_filetransfer/ftp');
        $ftp->setConfigModel($this)->downloadFile();
    }

}

?>

