<?php
/**
 * Copyright © Fiko Borizqy. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Fiko\AdvancedOrderNumber\Block\Adminhtml\System\Config;

use Fiko\AdvancedOrderNumber\Helper\Data as HelperData;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\FormKey;

/**
 * Reset Counter Number Block
 */
class ResetCounterNumber extends Field
{
    /**
     * @var FormKey
     */
    public $formKey;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @param Context $context
     * @param array $data
     * @param FormKey|null $formKey
     * @param HelperData|null $helperData
     */
    public function __construct(
        Context $context,
        array $data = [],
        ?FormKey $formKey = null,
        ?HelperData $helperData = null
    ) {
        parent::__construct($context, $data);

        $this->formKey = $formKey ?? ObjectManager::getInstance()->get(FormKey::class);
        $this->helperData = $helperData ?? ObjectManager::getInstance()->get(HelperData::class);
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setTemplate('Fiko_AdvancedOrderNumber::system/config/resetcounter.phtml');
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element = clone $element;
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $originalData = $element->getOriginalData();
        $currentCounters = [];

        foreach ($this->helperData->storeManager->getStores(true) as $store) {
            if ((int) $store->getId() === 0) {
                continue;
            }

            $currentCounters[$store->getId()] = $this->helperData->getCurrentCounter($store->getId());
        }

        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl('fiko_aon/resetcounter/order'),
                'form_key' => $this->formKey->getFormKey(),
                'current_counters' => $currentCounters,
            ]
        );

        return $this->_toHtml();
    }
}
