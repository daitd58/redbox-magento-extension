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

class CancelOrderAfter implements ObserverInterface
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
            $orderId = $order->getEntityId();
            $apiToken   = $this->helper->getApiToken();
            $businessId = $this->helper->getBusinessId();
            if ($orderId && $apiToken && $businessId) {
                $url = 'https://app.redboxsa.com/api/business/v1/cancel-shipment-by-order-id';
                $headers = ["Authorization: Bearer $apiToken", 'Content-Type: application/json'];
                $fields = [
                    'reference'			=> $orderId,
                    'business_id'		=> $businessId
                ];
                $fields_json = json_encode($fields);

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_json);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($curl);

                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->logger->info('cancelOrder' . $response);
                $this->logger->info('cancelOrder' . $status);

                curl_close($curl);
            }
        }
    }
}
