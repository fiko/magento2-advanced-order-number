<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Model;

use Exception;
use Fiko\AdvancedOrderNumber\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as SalesOrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;

class Order
{
    public $helper;
    protected $storeId;
    protected $salesOrderCollectionFactory;

    public function __construct(Data $helper, SalesOrderCollectionFactory $salesOrderCollectionFactory)
    {
        $this->helper = $helper;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->storeId = null;
    }

    public function customIncrementId($storeId = null): ?string
    {
        $this->storeId = $storeId !== null ? $storeId : $this->helper->storeManager->getStore()->getId();
        if (! $this->helper->getConfig(Data::PATH_ORDER_ENABLED, ScopeInterface::SCOPE_STORE, $this->storeId)) {
            return null;
        }

        $format = $this->helper->getConfig(Data::PATH_ORDER_FORMAT, ScopeInterface::SCOPE_STORE, $this->storeId);
        $counterPadding = (int) $this->helper->getConfig(
            Data::PATH_ORDER_COUNTER_PADDING,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
        $PaddingCharacter = (string) $this->helper->getConfig(
            Data::PATH_ORDER_PADDING_CHARACTER,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
        $nextCounter = (string) $this->helper->generateNextCounter($this->storeId);
        $nextCounter = str_pad($nextCounter, $counterPadding, $PaddingCharacter, STR_PAD_LEFT);

        $result = $this->setupFormat($format, $nextCounter);
        if ($this->validateIncrementId($result) === false) {
            $result = $this->customIncrementId($storeId);
        }

        return $result;
    }

    public function setupFormat($format, $counter)
    {
        $format = str_replace('{counter}', $counter, $format);

        return $this->helper->setupFormat($format, $counter, $this->storeId);
    }

    public function resetCounterNumber($storeId = null)
    {
        $stores = $storeId !== null ? [(int) $storeId] : $this->helper->storeManager->getStores(true);
        try {
            foreach ($stores as $store) {
                $storeId = is_int($store) ? $store : (int) $store->getId();
                if ($storeId === 0) {
                    continue;
                }
                $initialCounter = $this->helper->getConfig(
                    Data::PATH_ORDER_INITIAL_COUNTER,
                    ScopeInterface::SCOPE_STORES,
                    $storeId
                );
                $this->helper->setConfig(
                    Data::PATH_ORDER_CURRENT_COUNTER,
                    $initialCounter,
                    ScopeInterface::SCOPE_STORES,
                    $storeId
                );
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function validateIncrementId($incrementId)
    {
        $salesOrderCollection = $this->salesOrderCollectionFactory->create()
            ->addFieldToSelect('increment_id')
            ->addAttributeToFilter('increment_id', $incrementId);

        return $salesOrderCollection->count() === 0 ? true : false;
    }
}
