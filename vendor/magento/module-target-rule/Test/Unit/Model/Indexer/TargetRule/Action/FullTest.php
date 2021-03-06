<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  unit_tests
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TargetRule\Test\Unit\Model\Indexer\TargetRule\Action;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class for test full reindex target rule
 */
class FullTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test full reindex target rule
     */
    public function testFullReindex()
    {
        $objectManager = new ObjectManager($this);

        $ruleFactoryMock = $this->createPartialMock(
            \Magento\TargetRule\Model\RuleFactory::class,
            ['create']
        );

        $collectionFactoryMock = $this->createPartialMock(
            \Magento\TargetRule\Model\ResourceModel\Rule\CollectionFactory::class,
            ['create']
        );

        $productCollectionFactoryMock = $this->getMockBuilder(ProductCollectionFactory::class)
                ->disableOriginalConstructor()
                ->setMethods(['create'])
                ->getMock();

        $resourceMock = $this->createMock(\Magento\TargetRule\Model\ResourceModel\Index::class);

        $collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue([1, 2]));

        $resourceMock->expects($this->at(2))
            ->method('saveProductIndex')
            ->will($this->returnValue(1));

        $storeManagerMock = $this->getMockForAbstractClass(\Magento\Store\Model\StoreManagerInterface::class);
        $timezoneMock = $this->getMockForAbstractClass(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);

        $model = $objectManager->getObject(
            \Magento\TargetRule\Model\Indexer\TargetRule\Action\Full::class,
            [
                'ruleFactory' => $ruleFactoryMock,
                'ruleCollectionFactory' => $collectionFactoryMock,
                'resource' => $resourceMock,
                'storeManager' => $storeManagerMock,
                'localeDate' => $timezoneMock,
                'productCollectionFactory' => $productCollectionFactoryMock
            ]
        );

        $model->execute();
    }
}
