<?php
class Ccc_Locationcheck_Model_Observer
{
    public function checkLocation(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getId()) {
                $location = $product->getIsExcludeLocationCheck();
                if ($location == 237) {
                    $order->setProductExcludedLocationCheck(1);
                    $order->save();
                }
            }
        }
    }
    public function checkZip(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $productZipCode = $shippingAddress->getPostcode();
        $zipcodes = Mage::getModel("Ccc_Locationcheck/locationcheck")->getCollection()
            ->addFieldToFilter('zipcode', $productZipCode)
            ->getFirstItem();
        if ($zipcodes) {
            $order->setIsLocationChecked(1);
            $order->save();
        }
    }
}
