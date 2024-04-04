<?php

class Xyz_Test_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $a = Mage::getModel('test/abc');
        var_dump(get_class($a));

    }
}

?>