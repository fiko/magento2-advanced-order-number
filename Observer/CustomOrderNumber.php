<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Observer;

use Fiko\AdvancedOrderNumber\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomOrderNumber implements ObserverInterface
{
    /**
     * @var Order
     */
    public $order;

    /**
     * Constructor
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Generating new order number base on the configuration
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getData('order');
        $order->setIncrementId($this->order->generateIncrementId());
        $observer->setData('order', $order);
    }
}
