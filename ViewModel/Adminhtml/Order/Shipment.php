<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\ViewModel\Adminhtml\Order;

use Redbox\Shipping\Api\Data\AddressRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order;

class Shipment implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    protected $pointId;

    protected $order;

    protected $shippingAddress;

    /**
     * Shipment constructor.
     * @param AddressRepositoryInterface $addressRepository
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isShippingRedbox($order)
    {
        $this->order = $order;
        try {
            if ($this->order->getId()) {
                if ($this->order->getShippingMethod() == 'redbox_redbox') {
                    $quote = $this->quoteRepository->get($this->order->getQuoteId());
                    $this->shippingAddress = $this->order->getShippingAddress();
                    $quoteAddressId = $quote->getShippingAddress()->getId();
                    if ($quoteAddressId) {
                        $address = $this->addressRepository->getByQuoteAddressId($quoteAddressId);
                        $this->pointId = $address->getPointId();
                        $this->urlShippingLabel = $address->getUrlShippingLabel();
                        if ($this->pointId) {
                            return true;
                        }
                    }
                    return true;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;

    }

    /**
     * @return mixed
     */
    public function getPointId()
    {
        return $this->pointId;
    }

    /**
     * @return string
     */
    public function getPointAddress()
    {
        if ($this->getPointId()) {
            return sprintf(
                '%s, %s, %s, %s',
                $this->shippingAddress->getStreet()[0],
                $this->shippingAddress->getCompany(),
                $this->shippingAddress->getCity(),
                $this->shippingAddress->getPostCode()
            );
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getUrlShippingLabel() {
        return $this->urlShippingLabel;
    }
}
