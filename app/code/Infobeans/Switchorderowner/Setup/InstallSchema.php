<?php

/**
 * InfoBeans (India) Pvt. Ltd.
 *
 * @category   Infobeans
 * @package    Infobeans_Switchorderowner
 */

namespace Infobeans\Switchorderowner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $installer = $setup;
        $installer->startSetup();

        /**
         *
         * Create Table ib_switchorderowner_history
         */
        if ($installer->getConnection()->isTableExists('ib_switchorderowner_history') !== true) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('ib_switchorderowner_history')
                    )->addColumn('history_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'History ID'
                    )->addColumn('assign_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false,], 'Assign Time'
                    )->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'Order ID'
                    )->addColumn('is_notified', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['unsigned' => true, 'nullable' => false, 'default' => '0']
                    )->setComment('Swichorderowner History Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');

            $installer->getConnection()->createTable($table);
        }
        /**
         *
         * Create Table ib_switchorderowner_history_details
         */
        if ($installer->getConnection()->isTableExists('ib_switchorderowner_history_details') !== true) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('ib_switchorderowner_history_details')
                    )->addColumn('detail_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 15, ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'Detail ID'
                    )->addColumn('history_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['unsigned' => true, 'nullable' => false], 'History ID'
                    )->addColumn('data_key', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Data Key'
                    )->addColumn('from', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true]
                    )->addColumn('to', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true]
                    )->addForeignKey('FK_IB_AORDER_DETAIL_HISTORY', 'history_id', $installer->getTable('ib_switchorderowner_history'), 'history_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment('Swichorderowner History Details Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

}
