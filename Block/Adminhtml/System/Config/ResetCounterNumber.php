<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Block\Adminhtml\System\Config;

use Magento\AdvancedSearch\Block\Adminhtml\System\Config\TestConnection;

/**
 * Reset Counter Number Block
 */
class ResetCounterNumber extends TestConnection
{
    /**
     * @inheritdoc
     */
    protected function _getFieldMapping(): array
    {
        $fields = [
            'engine' => 'catalog_search_engine',
            'hostname' => 'catalog_search_elasticsearch7_server_hostname',
            'port' => 'catalog_search_elasticsearch7_server_port',
            'index' => 'catalog_search_elasticsearch7_index_prefix',
            'enableAuth' => 'catalog_search_elasticsearch7_enable_auth',
            'username' => 'catalog_search_elasticsearch7_username',
            'password' => 'catalog_search_elasticsearch7_password',
            'timeout' => 'catalog_search_elasticsearch7_server_timeout',
        ];

        return array_merge(parent::_getFieldMapping(), $fields);
    }
}
