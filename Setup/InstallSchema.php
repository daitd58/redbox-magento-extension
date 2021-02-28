<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Redbox Shipping InstallSchema class
 *
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{


    /**
     * Install schema
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $setup->getConnection()->addColumn(
            $setup->getTable($quoteTable),
            'point_id',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => 255,
                'comment' => 'Point ID',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable($orderTable),
            'point_id',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => 255,
                'comment' => 'Point ID',
            ]
        );

        $table = $setup->getConnection()
            ->newTable($setup->getTable('redbox_checkout_address'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ],
                'Entity ID'
            )
            ->addColumn(
                'shipping_address_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Quote Address ID'
            )
            ->addColumn('point_id', Table::TYPE_TEXT, 255, ['nullable' => false], 'Point Id')
            ->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('redbox_checkout_address'),
                    'shipping_address_id',
                    'quote_address',
                    'address_id'
                ),
                'shipping_address_id',
                $setup->getTable('quote_address'),
                'address_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Redbox Shipping Address Table');
        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addIndex(
            $setup->getTable('redbox_checkout_address'),
            $setup->getIdxName(
                'redbox_checkout_address',
                ['entity_id']
            ),
            ['entity_id']
        );

        $data = [];
        $statuses = [
            'redbox_expired'  => __('Redbox Expired'),
            'redbox_failed'  => __('Redbox Failed'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $setup->getConnection()
            ->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

        $states = [
            'complete' => [
                'label' => __('Complete'),
                'statuses' => [
                    'redbox_failed' => ['default' => '0']
                ],
                'visible_on_front' => true,
            ],
            'processing' => [
                'label' => __('Processing'),
                'statuses' => [
                    'redbox_expired' => ['default' => '0']
                ],
                'visible_on_front' => true,
            ]
        ];

        $data = [];
        foreach ($states as $code => $info) {
            if (isset($info['statuses'])) {
                foreach ($info['statuses'] as $status => $statusInfo) {
                    $data[] = [
                        'status' => $status,
                        'state' => $code,
                        'is_default' => 1,
                    ];
                }
            }
        }
        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default'],
            $data
        );

        $setup->endSetup();
    }//end install()


}//end class
