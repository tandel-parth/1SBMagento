<?php
require_once ('../app/Mage.php'); //Path to Magento
Mage::app();
echo "<pre>";

$connection = Mage::getSingleton('core/resource');
print_r(get_class_methods($connection));
print_r(get_class_methods(Mage::getSingleton('core/resource')->getConnection('core_write')));
print_r(get_class_methods(Mage::getSingleton('core/resource')->getConnection('core_read')));