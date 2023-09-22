<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Counter extends AbstractDb
{
    /**
     * Table name of counter number
     */
    const TABLE_NAME = 'fiko_aon_counter';

    /**
     * primary key of the table
     */
    const PRIMARY_KEY = 'entity_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
