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
            ['value' => 0, 'label' => __('Never')],
            ['value' => 1, 'label' => __('Daily')],
            ['value' => 2, 'label' => __('Weekly')],
            ['value' => 3, 'label' => __('Monthly')],
            ['value' => 4, 'label' => __('Annually')],
        ];
    }
}
