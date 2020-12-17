<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Redbox\Shipping\Model\Carrier\Inpost as Carrier;

/**
 * Class Data
 * @package MageToolkit\InPost\Helper
 */
class Data extends AbstractHelper
{
    const GOOGLE_MAPS_API_URL = 'https://maps.googleapis.com/maps/api/js';
    const XML_PATH_BUSINESS_ID_URL = 'carriers/inpost/business_id';
    const XML_PATH_DESCRIPTION = 'carriers/inpost/description';

    /**
     * @return string
     */
    public function getMethodCode()
    {
        return Carrier::CODE;
    }

    public function getDefaultCountry()
    {
        return trim($this->scopeConfig->getValue('general/country/default'));
    }

    /**
     * @return string
     */
    public function getBusinessId()
    {
        return trim($this->scopeConfig->getValue(static::XML_PATH_BUSINESS_ID_URL));
    }

    public function getDescription()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_DESCRIPTION);
    }
}
