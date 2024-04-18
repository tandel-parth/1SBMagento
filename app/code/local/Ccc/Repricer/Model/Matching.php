<?php
class Ccc_Repricer_Model_Matching extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_repricer/matching');
    }
    protected function _beforeSave()
    {
        if ($this->getData('reason') == 0 || $this->getData('reason') == 3) {
            $competitorUrl = $this->getData('competitor_url');
            $competitorSku = $this->getData('competitor_sku');
            $price = $this->getData('competitor_price');

            if (!empty($competitorUrl) && !empty($competitorSku) && $price == 0) {
                // Set reason to 3 (Not Available)
                $this->setData('reason', 3);
            } elseif (!empty($competitorUrl) && !empty($competitorSku) && $price != 0) {
                // Set reason to 1 (Active)
                $this->setData('reason', 1);
            }
        }

        return parent::_beforeSave();
    }
    public function getReason(){
        $reason = array(
            '0' => 'no match',
            '1' => 'active',
            '2' => 'out of stock',
            '3' => 'not available',
            '4' => 'rong match'
        );
        return $reason;
    }

}

?>