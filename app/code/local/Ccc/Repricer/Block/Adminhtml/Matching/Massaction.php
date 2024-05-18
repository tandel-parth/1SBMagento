<?php 

class Ccc_Repricer_Block_Adminhtml_Matching_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }
        
        $gridId = $this->getParentBlock()->getCollection()->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
        $gridId = $this->getParentBlock()->getCollection()->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
        $gridId = $this->getParentBlock()->getCollection()->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns([ 'pc_combine' => new Zend_Db_Expr('GROUP_CONCAT(CONCAT(product_id,"-",cpev.competitor_id) ORDER BY cpev.competitor_id SEPARATOR ",")')]);

        $gridIds = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchCol($gridId);

        if(!empty($gridIds)) {
            return join(",", $gridIds);
        }
        return '';
    }

}

?>