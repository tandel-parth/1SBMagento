<?php
class Ccc_Mostsellingrelateditems_Model_Observer
{
    public function setMostSelleingProduct(Varien_Event_Observer $observer)
    {
        $productObj =  $observer->getEvent()->getProduct();
        $productId = $productObj->getId();
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()) {
            $mostSellingItem = $product->getMostSelleingItem();
            $mostSellingItemValue = Mage::getModel('eav/config')->getAttribute('catalog_product', 'most_selleing_item')->getSource()->getOptionId('Yes');
            if ($mostSellingItem == $mostSellingItemValue) {
                $model = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')->getCollection()
                    ->addFieldToFilter('most_selling_product_id', $product->getId())->getData();
                if (!empty($model)) {
                    $productObj->addData(['has_most_selling_related_item' => 240]);
                }
            }
        }
        return $productObj;
    }
    public function setMostSellingItem(Varien_Event_Observer $observer)
    {
        $mostSelling =  $observer->getEvent()->getMostSelling();
        $product = Mage::getModel('catalog/product')->load($mostSelling->getMostSellingProductId());
        $mostSellingItemValue = Mage::getModel('eav/config')->getAttribute('catalog_product', 'most_selleing_item')->getSource()->getOptionId('Yes');
        if ($product->getMostSelleingItem() == $mostSellingItemValue) {
            $relatedItemValue = Mage::getModel('eav/config')->getAttribute('catalog_product', 'has_most_selling_related_item')->getSource()->getOptionId('Yes');
            $product->setHasMostSellingRelatedItem($relatedItemValue);
            $product->save();
        }
    }
}
