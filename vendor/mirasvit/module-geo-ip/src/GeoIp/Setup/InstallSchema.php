<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-geo-ip
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GeoIp\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\GeoIp\Api\Data\RuleInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $connection = $installer->getConnection();

        $installer->startSetup();

        $table = $connection->newTable(
            $installer->getTable(RuleInterface::TABLE_NAME)
        )->addColumn(
            RuleInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            RuleInterface::ID
        )->addColumn(
            RuleInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            RuleInterface::NAME
        )->addColumn(
            RuleInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            RuleInterface::DESCRIPTION
        )->addColumn(
            RuleInterface::SOURCE_TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            RuleInterface::SOURCE_TYPE
        )->addColumn(
            RuleInterface::SOURCE_CONDITION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            RuleInterface::SOURCE_CONDITION
        )->addColumn(
            RuleInterface::SOURCE_VALUE,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            RuleInterface::SOURCE_VALUE
        )->addColumn(
            RuleInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            RuleInterface::IS_ACTIVE
        )->addColumn(
            RuleInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            RuleInterface::PRIORITY
        )->addColumn(
            RuleInterface::ACTIONS,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            RuleInterface::ACTIONS
        );

        $connection->dropTable($setup->getTable(RuleInterface::TABLE_NAME));
        $connection->createTable($table);
    }
}
