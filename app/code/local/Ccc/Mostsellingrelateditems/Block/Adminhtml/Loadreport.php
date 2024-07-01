<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Loadreport extends Mage_Core_Block_Template
{
    public function getProductData()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->setOrder('entity_id', 'DESC');

        $productData = array();
        foreach ($collection as $product) {
            $productData[] = array(
                'entity_id' => $product->getId(),
                'name' => $product->getName(),
            );
        }

        return $productData;
    }
}
