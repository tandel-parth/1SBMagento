<?php
class Ccc_Filetransfer_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function localDir(){
        return Mage::getBaseDir('var').DS.'filezila';
   }
}

?>