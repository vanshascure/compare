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



namespace Mirasvit\GeoIp\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Api\Data\RuleInterfaceFactory;
use Mirasvit\GeoIp\Model\ResourceModel\Rule\CollectionFactory;

class RuleRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        RuleInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return RuleInterface[]|\Mirasvit\GeoIp\Model\ResourceModel\Rule\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return RuleInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return RuleInterface|false
     */
    public function get($id)
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        if (!$model->getId()) {
            return false;
        }

        return $model;
    }

    /**
     * @param RuleInterface $model
     *
     * @return RuleInterface
     */
    public function save(RuleInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param RuleInterface $model
     */
    public function delete(RuleInterface $model)
    {
        $this->entityManager->delete($model);
    }
}