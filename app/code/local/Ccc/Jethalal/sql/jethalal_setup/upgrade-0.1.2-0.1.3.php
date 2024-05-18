<?php
$installer = $this;
$installer->startSetup();
$attributeCode = 'soft';
$attributeLabel = 'Soft';
$data = array(
    'attribute_code'  => $attributeCode,
    'type'            => 'varchar', // Correct key for backend_type
    'input'           => 'text', // Correct key for frontend_input
    'label'           => $attributeLabel, // Simplified label key
    'source'          => 'eav/entity_attribute_source_table', // Ensure that the source model exists and is correctly specified
    'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'        => false,
    'configurable'    => false,
    'apply_to'        => 'simple,configurable',
    'visible_on_front'=> true,
    'user_define'=> true,
    'searchable'      => false,
    'filterable'      => false,
    'comparable'      => false,
    'used_for_promo_rules' => false,
    'is_html_allowed_on_front' => true,
);

$installer->addAttribute('catalog_product', $attributeCode, $data);

$installer->endSetup();