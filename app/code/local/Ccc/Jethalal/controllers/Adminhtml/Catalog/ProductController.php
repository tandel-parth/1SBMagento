<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class Ccc_Jethalal_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function indexAction()
    {
        // Your custom logic here
        echo "This is the overridden product index action!";
        // Call parent action
        parent::indexAction();
    }
}