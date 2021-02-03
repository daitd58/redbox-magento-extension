<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $redboxTable = 'redbox_checkout_address';
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($redboxTable),
                    'url_shipping_label',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' =>'Url Shipping Label'
                    ]
                );
        }

        $setup->endSetup();
    }
}
