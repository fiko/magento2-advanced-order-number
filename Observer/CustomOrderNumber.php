<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomOrderNumber implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        /** @var string $data */
        $order = $observer->getData('order');
        $order->setIncrementId('NEWPREFIX-' . $order->getIncrementId());
        $observer->setData('order', $order);
    }
}
