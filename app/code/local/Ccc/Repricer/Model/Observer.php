<?php

class Ccc_Repricer_Model_Observer
{

    CONST TIME_SPAN=24*3600;
    public function setCompetitorData()
    {   
        $timestamp = time();
        $sqlTimestamp = date('Y-m-d H:i:s', $timestamp);
        $pdata = [];
        $data = (Mage::getModel('ccc_repricer/competitors')->getCollection());
        foreach ($data as $_data) {
            $difference = strtotime($sqlTimestamp) - strtotime($_data->getCreatedAt());
            if ($difference <= Ccc_Repricer_Model_Observer::TIME_SPAN) {
                $productdata = Mage::getModel('catalog/product')->getCollection();
                $productdata->addAttributeToSelect(['name', 'status']);
                $productdata->addAttributeToFilter('status', 1);
                echo "<pre>";
                foreach ($productdata as $_product) {
                    // Initialize $pdata for each product
                    $pdata[] = [
                        'competitor_id' => $_data->getCompetitorId(),
                        'product_id' => $_product->getId(),
                    ];
                }
            }
        }
        if (!empty($pdata)) {
            $matchingModel = Mage::getModel('ccc_repricer/matching');
            foreach ($pdata as $data) {
                $matchingModel->setData($data);
                $matchingModel->save();
            }
        } else {
            echo "No data to save.";
        }
    }

    public function setProductData()
    {
        $timestamp = time();
        $sqlTimestamp = date('Y-m-d H:i:s', $timestamp);
        $pdata = [];
        $data = (Mage::getModel('catalog/product')->getCollection());
        $data->addAttributeToSelect(['name', 'status']);
        $data->addAttributeToFilter('status', 1);
        foreach ($data as $_product) {
            $difference = strtotime($sqlTimestamp) - strtotime($_product->getCreatedAt());
            if ($difference <= Ccc_Repricer_Model_Observer::TIME_SPAN) {
                $cdata = Mage::getModel('ccc_repricer/competitors')->getCollection();
                foreach ($cdata as $_data) {
                    $timediff=strtotime($sqlTimestamp) - strtotime($_data->getCreatedAt());
                    if($timediff >=Ccc_Repricer_Model_Observer::TIME_SPAN){
                    $pdata[] = [
                        'competitor_id' => $_data->getCompetitorId(),
                        'product_id' => $_product->getId(),
                    ];
                }
                }
            }
        }
        if (!empty($pdata)) {
            $matchingModel = Mage::getModel('rccc_epricer/matching');
            foreach ($pdata as $data) {
                $matchingModel->setData($data);
                $matchingModel->save();
            }
        } else {
            echo "No data to save.";
        }
    }
}