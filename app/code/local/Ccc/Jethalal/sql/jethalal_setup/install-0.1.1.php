<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mailto:license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Ccc
 * @package     ccc_repricer_competitor
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * drop table 'ccc/repricer_competitor' if it exist
 */
$tableName = $installer->getTable('ccc_jethalal_jalebi');
if ($installer->getConnection()->isTableExists($tableName)) {
    echo $tableName;
    // Table already exists, log a message and return
    Mage::log("Table $tableName already exists", null, 'ccc_jethalal_jalebi.log');
    return;
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ccc_jethalal_jalebi'))
    ->addColumn('jalebi_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Jalebi Id')
    ->addColumn('jalebi_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Jalebi Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'default' => '2',
    ), 'Jalebi Status')
    ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Jalebi Created Date')
    ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE,
    ), 'Jalebi Updated Date')
    ->setComment('CCC Jethalal Jalebi Table');
Mage::log($table, null, 'ccc_jethalal_jalebi.log');
$installer->getConnection()->createTable($table);
$installer->endSetup();
