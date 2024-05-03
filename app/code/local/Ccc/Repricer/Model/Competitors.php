<?php
class Ccc_Repricer_Model_Competitors extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/competitors');
    }
    public function getCompetitorArray()
    {
        $allCompetitors = $this->getCollection();
        $result = [];
        foreach ($allCompetitors as $competitor) {
            $result[$competitor->getCompetitorId()] = $competitor->getName();
        }
        return $result;
    }

}

?>
