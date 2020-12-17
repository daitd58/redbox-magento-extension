<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Helper;

class Lockers
{
    public $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;
    }

    public function isActive()
    {
        return (bool)$this->scopeConfig->getValue(
            'carriers/inpost/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiToken()
    {
        return $this->scopeConfig->getValue(
            'carriers/inpost/api_token',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getBusinessId()
    {
        return $this->scopeConfig->getValue(
            'carriers/inpost/business_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getLabelsFormat()
    {
        return 'pdf';
    }

    public function isDebug()
    {
        return $this->scopeConfig->getValue(
            'carriers/inpost/debug',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMetric()
    {
        return $this->scopeConfig->getValue(
            'carriers/inpost/weight',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
