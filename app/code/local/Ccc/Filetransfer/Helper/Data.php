<?php
class Ccc_Filetransfer_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function localDir(){
        return Mage::getBaseDir('var').DS.'filezila';
   }
   public function csvData(){
      $data = array(
         'part_number' =>'itemIdentification.itemIdentifier:itemNumber',
         'depth' =>'itemIdentification.itemCharacteristics.itemDimensions.depth:value',
         'height' =>'itemIdentification.itemCharacteristics.itemDimensions.height:value',
         'length' =>'itemIdentification.itemCharacteristics.itemDimensions.length:value',
         'weight' =>'itemIdentification.itemCharacteristics.itemDimensions.weight:value'
      );
      return $data;
   }
}

?>