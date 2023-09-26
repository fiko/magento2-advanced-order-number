<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ResetBehaviour implements ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0 0 31 2 *', 'label' => __('Never')],
            ['value' => '0 0 * * *', 'label' => __('Daily')],
            ['value' => '0 0 * * 1', 'label' => __('Weekly')],
            ['value' => '0 0 1 * *', 'label' => __('Monthly')],
            ['value' => '0 0 * 1 *', 'label' => __('Annually')],
        ];
    }
}
