<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        //Install schema logic
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('redbox_locker')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('redbox_locker')
            )
                ->addColumn(
                    'locker_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Increment ID'
                )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Locker ID'
                )
                ->addColumn(
                    'post_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Post Code'
                )
                ->addColumn(
                    'district',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'District'
                )
                ->addColumn(
                    'street',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Street'
                )
                ->addColumn(
                    'city',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'City'
                )
                ->addColumn(
                    'location_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Location Type'
                )
                ->addColumn(
                    'latitude',
                    \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    null,
                    ['nullable => false'],
                    'Latitude'
                )
                ->addColumn(
                    'longitude',
                    \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    null,
                    ['nullable => false'],
                    'Longitude'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Description'
                )
                ->addColumn(
                    'open_hour',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Open Hour'
                )
                ->addColumn(
                    'industry',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Industry'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Status'
                )
                ->addColumn(
                    'point_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Point Name'
                )
                ->addColumn(
                    'host_name_en',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Host Name EN'
                )
                ->addColumn(
                    'host_name_ar',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Host Name AR'
                )
                ->addColumn(
                    'is_public',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    ['nullable => false'],
                    'Is Public'
                )
                ->addColumn(
                    'accept_payment',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    ['nullable => false'],
                    'Accept Payment'
                )
                ->addColumn(
                    'icon',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Icon'
                )
                ->addColumn(
                    'alert_message_en',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Alert Message EN'
                )
                ->addColumn(
                    'alert_message_ar',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable => false'],
                    'Alert Message AR'
                )
                ->addColumn(
                    'estimate_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable => false'],
                    'Estimate Time'
                )
                ->setComment('Lockers');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $setup->getTable('redbox_locker'),
                $setup->getIdxName(
                    $installer->getTable('redbox_locker'),
                    ['id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}