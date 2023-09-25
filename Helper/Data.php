<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Helper;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data
{
    public const PATH_ORDER_ENABLED = 'fiko_advanced_order_number/order/enabled';
    public const PATH_ORDER_FORMAT = 'fiko_advanced_order_number/order/format';
    public const PATH_ORDER_INITIAL_COUNTER = 'fiko_advanced_order_number/order/initial_counter_number';
    public const PATH_ORDER_INCREMENTAL_COUNTER = 'fiko_advanced_order_number/order/incremental_counter_number';
    public const PATH_ORDER_COUNTER_PADDING = 'fiko_advanced_order_number/order/counter_padding';
    public const PATH_ORDER_PADDING_CHARACTER = 'fiko_advanced_order_number/order/padding_character';
    public const PATH_ORDER_RESET_BEHAVIOUR = 'fiko_advanced_order_number/order/reset_counter_number_behaviour';
    public const PATH_ORDER_CURRENT_COUNTER = 'fiko_advanced_order_number/order/current_counter';

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var WriterInterface
     */
    public $configWriter;

    /**
     * @var CollectionFactory
     */
    public $configCollectionFactory;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param CollectionFactory $configCollectionFactory
     * @param DateTime $dateTime
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        CollectionFactory $configCollectionFactory,
        DateTime $dateTime,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieving configuration value
     *
     * @param string $path Configuration path we want to retrieve
     * @param string $scope (default: "store")
     * @param int|null $scopeId (default: null)
     * @return mixed
     */
    public function getConfig($path, $scope = ScopeInterface::SCOPE_STORE, $scopeId = null): mixed
    {
        return $this->scopeConfig->getValue($path, $scope, $scopeId);
    }

    /**
     * Update configuration
     *
     * @param string $path Configuration path we want to update
     * @param mixed $value New value of the configuration path
     * @param string $scope Scope of the configuration (store||website||default, default: "default")
     * @param integer $scopeId Scope ID of the $scope
     * @return void
     */
    public function setConfig($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0)
    {
        $this->configWriter->save($path, $value, $scope, $scopeId);
    }

    /**
     * Retrieve current counter value
     *
     * @param int $scopeId
     * @return int
     */
    public function getCurrentCounter($scopeId): int
    {
        $initialCounter = $this->getConfig(self::PATH_ORDER_INITIAL_COUNTER, ScopeInterface::SCOPE_STORES, $scopeId);

        // fetching configuration by ignoring cache
        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addFieldToFilter('scope', ScopeInterface::SCOPE_STORES);
        $configCollection->addFieldToFilter('scope_id', $scopeId);
        $configCollection->addFieldToFilter('path', self::PATH_ORDER_CURRENT_COUNTER);
        $configCollection->addFieldToSelect('value');
        $currentCounter = (int) $configCollection->load()->getFirstItem()->getValue();

        if ($currentCounter === null) {
            $this->setConfig(self::PATH_ORDER_CURRENT_COUNTER, $initialCounter, ScopeInterface::SCOPE_STORES, $scopeId);
            $currentCounter = 1;
        }

        return $currentCounter;
    }

    /**
     * Generate next counter for next order
     *
     * @param int $scopeId
     * @return int
     */
    public function generateNextCounter($scopeId): int
    {
        $currentCounter = (int) $this->getCurrentCounter($scopeId);
        $incrementalCounter = (int) $this->getConfig(
            self::PATH_ORDER_INCREMENTAL_COUNTER,
            ScopeInterface::SCOPE_STORES,
            $scopeId
        );
        $incrementalCounter = ($incrementalCounter > 0) ? $incrementalCounter : 1;
        $nextCounter = $currentCounter + $incrementalCounter;

        $this->setConfig(self::PATH_ORDER_CURRENT_COUNTER, $nextCounter, ScopeInterface::SCOPE_STORES, $scopeId);

        return $nextCounter;
    }

    public function setupFormat($format, $counter, $storeId)
    {
        $currentDate = $this->dateTime->date();
        $currentStore = $this->storeManager->getStore($storeId);
        $currentGroup = $currentStore->getGroup();
        $currentWebsite = $currentStore->getWebsite();
        $replaces = [
            '{d}' => $this->dateTime->date('j', $currentDate),
            '{dd}' => $this->dateTime->date('d', $currentDate),
            '{m}' => $this->dateTime->date('n', $currentDate),
            '{mm}' => $this->dateTime->date('m', $currentDate),
            '{yy}' => $this->dateTime->date('y', $currentDate),
            '{yyyy}' => $this->dateTime->date('Y', $currentDate),
            '{storeId}' => $currentStore->getId(),
            '{storeCode}' => $currentStore->getCode(),
            '{groupId}' => $currentGroup->getId(),
            '{groupCode}' => $currentGroup->getCode(),
            '{websiteId}' => $currentWebsite->getId(),
            '{websiteCode}' => $currentWebsite->getCode(),
        ];

        return str_ireplace(array_keys($replaces), array_values($replaces), $format);
    }
}
