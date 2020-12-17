<?php

namespace Redbox\Shipping\Plugin\Order;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Order\Interceptor;
use Redbox\Shipping\Api\Data\AddressRepositoryInterface;
use Redbox\Shipping\Helper\Points;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class PlaceAfterPlugin
{
    private $helper;
    private $quoteFactory;
    private $addressRepository;
    private $curl;
    private $logger;

    public function __construct(
        Points $helper,
        QuoteFactory $quoteFactory,
        AddressRepositoryInterface $addressRepository,
        PsrLoggerInterface $logger,
        Curl $curl
    ) {
        $this->helper = $helper;
        $this->quoteFactory = $quoteFactory;
        $this->addressRepository = $addressRepository;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
    * @param OrderManagementInterface $orderManagementInterface
    * @param Interceptor $order
    * @return $order
    */
    public function afterPlace(OrderManagementInterface $orderManagementInterface, $order)
    {
        $orderId = $order->getId();

        if ($order->getShippingMethod() == 'redbox_redbox' && $this->helper->isActive()) {
            $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
            $quoteAddressId = $quote->getShippingAddress()->getId();
            if ($quoteAddressId) {
                $apiToken   = $this->helper->getApiToken();
                $businessId = $this->helper->getBusinessId();
                $shippingAddress = $order->getShippingAddress();
                $billingAddress = $order->getBillingAddress();
                $pointId = $this->addressRepository->getByQuoteAddressId($quoteAddressId)->getPointId();

                // do something with order object (Interceptor )
                if ($apiToken) {
                    $createShipmentUrl = 'https://app.redboxsa.com/api/business/v1/create-shipment';
                    $items = [];
                    $orderProducts = $order->getAllItems();

                    foreach ($orderProducts as $orderProduct) {
                        array_push($items, [
                            'name' => $orderProduct->getName(),
                            'quantity' => $orderProduct->getQtyOrdered(),
                            'unitPrice' => $orderProduct->getPrice()
                        ]);
                    }

                    $fields = [
                        'reference' => $order->getEntityId(),
                        'point_id' => $pointId,
                        'sender_name' => $billingAddress->getFirstName() . ' ' . $billingAddress->getLastName(),
                        'sender_email' => $billingAddress->getEmail(),
                        'sender_phone' => $billingAddress->getTelephone(),
                        'sender_address' => $billingAddress->getStreet()[0] . ' ' . $billingAddress->getCity() . ' ' . $billingAddress->getCountryId(),
                        'customer_name' => $shippingAddress->getFirstName() . ' ' . $shippingAddress->getLastName(),
                        'customer_phone' => $shippingAddress->getTelephone(),
                        'customer_address' => $shippingAddress->getStreet()[0] . ' ' . $shippingAddress->getCity() . ' ' . $shippingAddress->getCountryId(),
                        'cod_currency' => $order->getOrderCurrencyCode(),
                        'cod_amount' => $order->getGrandTotal(),
                        'items' => $items,
                        'from_platform' => 'magento'
                    ];

                    if ($businessId) {
                        $fields['business_id'] = $businessId;
                    }
                    $fields_json = json_encode($fields);
                    $headers = [
                        "Content-Type" => "application/json",
                        "Authorization" => "Bearer " . $apiToken
                    ];
                    $this->curl->setHeaders($headers);
                    $this->curl->post($createShipmentUrl, $fields_json);
                    $response = $this->curl->getBody();
                    $this->logger->info('field----' . json_encode($fields));
                    $this->logger->info('response----' . $response);
                }
            }
        }

        return $order;
    }
}
