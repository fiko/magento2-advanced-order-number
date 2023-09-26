<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fiko\AdvancedOrderNumber\Controller\Adminhtml\ResetCounter;

use Exception;
use Fiko\AdvancedOrderNumber\Helper\Data as Helper;
use Fiko\AdvancedOrderNumber\Model\Order as CounterOrder;
use Laminas\Http\AbstractMessage;
use Laminas\Http\Response;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 */
class Order extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var CounterOrder
     */
    protected $counterOrder;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CounterOrder $counterOrder
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        ?JsonFactory $jsonFactory = null,
        ?CounterOrder $counterOrder = null,
        ?Helper $helper = null
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory ?? ObjectManager::getInstance()->get(JsonFactory::class);
        $this->counterOrder = $counterOrder ?? ObjectManager::getInstance()->get(CounterOrder::class);
        $this->helper = $helper ?? ObjectManager::getInstance()->get(Helper::class);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $counters = [];
        try {
            $this->counterOrder->resetCounterNumber();
            $stores = $this->helper->storeManager->getStores(true);
            foreach ($stores as $store) {
                if ((int) $store->getId() === 0) {
                    continue;
                }
                $counters[$store->getId()] = $this->helper->getCurrentCounter($store->getId());
            }
            return $resultJson->setData([
                'message' => __('Succeeded to Reset Counter Number.'),
                'counters' => $counters,
            ]);
        } catch (Exception $e) {
            $resultJson->setStatusHeader(
                Response::STATUS_CODE_500,
                AbstractMessage::VERSION_11,
                __('Internal Server Error.')
            );
            return $resultJson->setData(
                [
                    'message' => $e->getMessage(),
                    'errorcode' => Response::STATUS_CODE_500
                ]
            );
        }
    }
}
