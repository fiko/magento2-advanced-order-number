<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Fiko\AdvancedOrderNumber\Model\Order;

class CustomOrderNumber implements ObserverInterface
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function execute(Observer $observer): void
    {
        /** @var string $data */
        $order = $observer->getData('order');
        $order->setIncrementId($this->order->customIncrementId());
        $observer->setData('order', $order);
    }
}
