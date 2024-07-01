<?php
class Ccc_Mostsellingrelateditems_Block_Adminhtml_Mostselling_Productdata extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('mostsellingrelateditems/productdata.phtml');
    }
    public function getProductData()
    {
        $productId = $this->getRequest()->getPost('id');
        $data = [];
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()) {
            $data = array(
                'product_id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
            );
        }

        return $data;
    }
    public function relatedProduct()
    {
        $collection =   Mage::getModel('ccc_mostsellingrelateditems/mostsellingrelateditems')
            ->getCollection()
            ->addFieldToFilter('most_selling_product_id', $this->getProductData()['product_id'])
            ->getData();
        if (!empty($collection)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
