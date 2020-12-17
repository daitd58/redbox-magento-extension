<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Helper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Points
 *
 * @package Redbox\Shipping\Helper
 */
class Points
{

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;


    /**
     * Points constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;

    }//end __construct()


    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->scopeConfig->getValue(
            'carriers/redbox/active',
            ScopeInterface::SCOPE_STORE
        );

    }//end isActive()


    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->scopeConfig->getValue(
            'carriers/redbox/api_token',
            ScopeInterface::SCOPE_STORE
        );

    }//end getApiToken()


    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->scopeConfig->getValue(
            'carriers/redbox/business_id',
            ScopeInterface::SCOPE_STORE
        );

    }//end getBusinessId()


}//end class
