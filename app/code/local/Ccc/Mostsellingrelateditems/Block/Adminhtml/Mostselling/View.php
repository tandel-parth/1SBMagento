<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('mostsellingrelateditems/view.phtml');
    }
    public function getProductData()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')->getCollection();
        $collection->addFieldToFilter('id', $id);

        $attributeCode = 'name';
        $entityType = 'catalog_product';
        $attribute = Mage::getModel('eav/config')->getAttribute($entityType, $attributeCode);
        $attributeId = $attribute->getAttributeId();

        $collection->getSelect()->join(
            array('CPEV' => 'catalog_product_entity_varchar'),
            'main_table.most_selling_product_id = CPEV.entity_id AND CPEV.attribute_id = ' . (int)$attributeId,
            array('most_selling_product_name' => 'CPEV.value')
        );
        $collection->getSelect()->join(
            array('CPEVR' => 'catalog_product_entity_varchar'),
            'main_table.related_product_id = CPEVR.entity_id AND CPEVR.attribute_id = ' . (int)$attributeId,
            array('related_product_name' => 'CPEVR.value')
        );
        return $collection;
    }
}
