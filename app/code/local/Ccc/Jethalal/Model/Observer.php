<?php

class Ccc_Jethalal_Model_Observer
{
    public function jalebiJethalal(Varien_Event_Observer $observer)
    {
        $model =  Mage::getModel("Customer/Session");
        return $model->logout();
    }
    public function setJalebi(){
        // $model =  Mage::getModel("Customer/Session");
        // $customerID = $model->getCustomer();
        // Mage::dispatchEvent('customer_logout', array('customer' => $customerID) );
    }
}
