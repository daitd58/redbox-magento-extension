<?php
/**
  * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Quote\Model\QuoteFactory;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Redbox\Shipping\Api\Data\AddressRepositoryInterface;
use Redbox\Shipping\Helper\Points;

class SalesPlaceOrderAfter implements ObserverInterface
{
    private $quoteFactory;
    private $helper;
    private $logger;
    private $addressRepository;
    private $curl;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        QuoteFactory $quoteFactory,
        Points $helper,
        PsrLoggerInterface $logger,
        Curl $curl
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->addressRepository = $addressRepository;
        $this->quoteFactory = $quoteFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        if ($order->getShippingMethod() == 'redbox_redbox' && $this->helper->isActive()) {
            $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
            $quoteAddressId = $quote->getShippingAddress()->getId();
            if ($quoteAddressId) {
                $shippingAddress = $order->getShippingAddress();
                $pointId = $this->addressRepository->getByQuoteAddressId($quoteAddressId)->getPointId();
                $apiToken   = $this->helper->getApiToken();
                $url = 'https://app.redboxsa.com/api/business/v1/get-point-detail?point_id=' . $pointId;

                if ($apiToken) {
                    $headers = [
                        "Content-Type" => "application/json",
                        "Authorization" => "Bearer " . $apiToken
                    ];
                    $this->curl->setHeaders($headers);
                    $this->curl->get($url);
                    $result = json_decode($this->curl->getBody(), true);
                    $shippingAddress->setPostcode($result['point']['address']['postCode']);
                    $shippingAddress->setStreet(
                        $result['point']['host_name_en'] . " (" . $result['point']['point_name'] . "), " .
                            $result['point']['address']['street']
                    );
                    $shippingAddress->setCity($result['point']['address']['city']);
                    $shippingAddress->setCustomerAddressId(null);
                    $shippingAddress->setCompany($result['point']['address']['district']);
                    $shippingAddress->save();
                }
            }
        }
    }
}
