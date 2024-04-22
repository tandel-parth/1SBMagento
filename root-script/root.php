<?php
require_once ('../app/Mage.php'); //Path to Magento
Mage::app();

// $folderPath = Mage::getBaseDir('var') . DS . 'report' . DS . 'cmonitor' . DS . 'download';
// // Scan the folder for CSV files added in the last 24 hours
// $files = glob($folderPath . DIRECTORY_SEPARATOR . '*_pending.csv');

// foreach ($files as $file) {
//     $row = 0;
//     $header = [];
//     if (($handle = fopen($file, 'r')) !== FALSE) {
//         $mainData = [];
//         while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
//             if (!$row) {
//                 // First row contains headers
//                 foreach ($data as &$item) {
//                     $item = str_replace(' ', '_', strtolower($item));
//                 }
//                 $header = $data;
//                 $row++;
//                 continue;
//             }
//             // Combine headers with data for current row
//             $rowData = array_combine($header, $data);
//             $mainData[] = $rowData;
//         }
//         fclose($handle);
//     }
//     $model = Mage::getModel('ccc_repricer/matching');
//     $competitorCollection = Mage::getModel('ccc_repricer/competitors')->getCollection()->addFieldToFilter('name', $rowData['competitor_name'])->getData();
//     $competitorId = "";
//     foreach ($competitorCollection as $competitor) {
//         $competitorId = $competitor['competitor_id'];
//     }
//     $collection = $model->getCollection()
//         ->addFieldToFilter('competitor_sku', array('in' => array_column($mainData, 'competitor_sku')))
//         ->addFieldToFilter('competitor_id', $competitorId);

//     // Create an associative array to map competitor_sku to repricer_id
//     $skuToRepricerIdMap = array();
//     foreach ($collection as $item) {
//         $skuToRepricerIdMap[$item->getCompetitorSku()] = $item->getRepricerId();
//     }
//     foreach ($mainData as &$saveData) {
//         if (isset($skuToRepricerIdMap[$saveData['competitor_sku']])) {
//             $saveData['repricer_id'] = $skuToRepricerIdMap[$saveData['competitor_sku']];
//         } else {
//             Mage::log("No repricer_id found for competitor_sku: " . $saveData['competitor_sku']);
//         }

//         $csvData[] = $saveData;
//     }

//      // Start building the UPDATE query
//      $updateQuery = "UPDATE " . Mage::getSingleton('core/resource')->getTableName('ccc_repricer/matching') . " SET ";

//      // Construct the SET clause with conditional statements for each column
//      $caseStatements = array();
//      foreach ($csvData as $dataArray) {
//          unset($dataArray['product_name']);
//          unset($dataArray['competitor_name']);
//          $repricerId = $dataArray['repricer_id'];
//          unset($dataArray['repricer_id']);
//          foreach ($dataArray as $column => $value) {
//              // Construct conditional statement for each column
//              $caseStatements[] = "`$column` = CASE WHEN `repricer_id` = '$repricerId' THEN '$value' ELSE `$column` END";
//          }
//      }

//      // Combine CASE statements into the SET clause
//      $updateQuery .= implode(', ', $caseStatements);

//      // Construct the WHERE clause to specify repricer_ids
//      $repricerIds = array_column($csvData, 'repricer_id');
//      $updateQuery .= " WHERE `repricer_id` IN (" . implode(',', $repricerIds) . ")";

//      // Execute the UPDATE query
//      $writeAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');
//      $writeAdapter->query($updateQuery);
// }


echo "<pre>";
print_r($competitorId);
die("done");
