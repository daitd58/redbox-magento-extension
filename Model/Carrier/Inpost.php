<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Inpost extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    const CODE = 'redbox';

    // @codingStandardsIgnoreStart
    protected $_code = 'redbox';
    // @codingStandardsIgnoreEnd

    /**
     * Available carrier method
     *
     * @var string
     */
    const METHOD = 'shipping';
    /** @var \Magento\Framework\App\ResponseFactory */
    private $responseFactory;
    /** @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory */
    private $rateMethodFactory;
    /** @var \Magento\Shipping\Model\Rate\ResultFactory */
    private $rateResultFactory;
    /** @var \Redbox\Shipping\Helper\Data */
    private $helper;
    /** @var \Redbox\Shipping\Helper\Lockers */
    private $configHelper;

    /**
     * Inpost constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Redbox\Shipping\Helper\Data $helper,
        \Redbox\Shipping\Helper\Lockers $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->helper = $helper;
        $this->rateResultFactory = $rateResultFactory;
        $this->responseFactory = $responseFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        /** @var \Magento\Shipping\Model\Rate\Result $res
         * \`ult
         */
        $result = $this->rateResultFactory->create();

        if ($this->configHelper->getApiToken()) {
            if (!$this->getConfigFlag('active')) {
                return false;
            }

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier(self::CODE);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod(self::CODE);
            $method->setMethodTitle($this->getConfigData('name'));

            $amount = $this->getConfigData('price');
            if ($request->getFreeShipping()) {
                $amount = 0;
            }

            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);
        }

        return $result;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }
}
