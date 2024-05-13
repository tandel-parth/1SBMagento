<?php
class Ccc_Repricer_Block_Adminhtml_Matching_Widget_Grid_Massaction_Abstract
    extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    /**
     * Retrieve JSON string of selected checkboxes
     *
     * @return string
     */
    public function __construct(){
        echo 123;
        die("done");
    }
    public function getGridIdsJson()
    {
        echo 123;
        die("done");
        // Your custom implementation here
        // For example, let's assume you want to return a hardcoded JSON string
        return Mage::helper('core')->jsonEncode(array('1', '2', '3'));
    }
}