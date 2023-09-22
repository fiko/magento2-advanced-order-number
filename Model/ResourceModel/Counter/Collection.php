<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Model\ResourceModel\Counter;

use Fiko\AdvancedOrderNumber\Model\Counter;

use Fiko\AdvancedOrderNumber\Model\ResourceModel\Counter as ResourceModelCounter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = ResourceModelCounter::PRIMARY_KEY;
    protected $_eventPrefix = 'fiko_advancedordernumber_counter_collection';
    protected $_eventObject = 'counter_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Counter::class, ResourceModelCounter::class);
    }
}
