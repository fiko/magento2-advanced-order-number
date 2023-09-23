<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Model;

use Fiko\AdvancedOrderNumber\Helper\Data;
use Magento\Store\Model\ScopeInterface;

class Order
{
    public $helper;
    protected $storeId;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
        $this->storeId = null;
    }

    public function customIncrementId($storeId = null): ?string
    {
        $this->storeId = $storeId !== null ? $storeId: $this->helper->storeManager->getStore()->getId();
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

        return $this->setupFormat($format, $nextCounter);
    }

    public function setupFormat($format, $counter)
    {
        $format = str_replace('{{counter}}', $counter, $format);

        return $this->helper->setupFormat($format, $counter, $this->storeId);
    }
}
