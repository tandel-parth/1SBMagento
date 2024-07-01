<?php
require_once('../app/Mage.php'); //Path to Magento
Mage::app();
echo "<pre>";

$model = Mage::getSingleton('core/resource')->getConnection('core_write');
print_r(get_class($model));
