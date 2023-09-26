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
    /**
     * @var Data
     */
    public $helper;

    /**
     * @var SalesOrderCollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * @var int store view id
     */
    protected $storeId;

    /**
     * Constructor
     *
     * @param Data $helper
     * @param SalesOrderCollectionFactory $salesOrderCollectionFactory
     */
    public function __construct(Data $helper, SalesOrderCollectionFactory $salesOrderCollectionFactory)
    {
        $this->helper = $helper;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->storeId = null;
    }

    /**
     * generating new next order number base from the configuration
     *
     * @param int|null $storeId Store view id
     * @return string|null
     */
    public function generateIncrementId($storeId = null): ?string
    {
        /** @var int store view id */
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
            $result = $this->generateIncrementId($storeId);
        }

        return $result;
    }

    /**
     * Replacing order number format to get the expected result of increment id
     * format.
     *
     * @param string $format
     * @param string|int $counter
     * @return string
     */
    public function setupFormat($format, $counter): string
    {
        $format = str_replace('{counter}', $counter, $format);

        return $this->helper->setupFormat($format, $counter, $this->storeId);
    }

    /**
     * Crontab of reset order will execute this method on the first place
     *
     * @return void
     */
    public function scheduledResetCounterNumber(): void
    {
        $this->resetCounterNumber();
    }

    /**
     * resetting counter number of custom order number
     *
     * @param int|null $storeId
     * @return void
     *
     * @throws Exception
     */
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

    /**
     * Checking any existing orders if the next Increment Id already used by the
     * existing order.
     *
     * @param string $incrementId
     * @return bool
     */
    public function validateIncrementId($incrementId): bool
    {
        $salesOrderCollection = $this->salesOrderCollectionFactory->create()
            ->addFieldToSelect('increment_id')
            ->addAttributeToFilter('increment_id', $incrementId);

        return $salesOrderCollection->count() === 0 ? true : false;
    }
}
