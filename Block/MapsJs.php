<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block;

use Magento\Framework\View\Element\Template;
use Redbox\Shipping\Helper\Data;

class MapsJs extends Template
{

    /**
     * @var Data
     */
    private $helper;
    /** @var Template\Context  */
    private $context;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->context = $context;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getGoogleMapsApiUrl()
    {
        return $this->helper->getGoogleMapsApiUrl();
    }

    /**
     * @return string
     */
    public function getDefaultCountry()
    {
        return $this->helper->getDefaultCountry();
    }

    public function getInpostDescription()
    {
        return $this->helper->getDescription();
    }

    public function getPhoneUpdateUrl()
    {
        return $this->context->getStoreManager()->getStore()->getUrl('locker/quote/setphone');
    }
}
