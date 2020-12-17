<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Controller\Quote;

use Magento\Framework\App\Action\Context;

class Setphone extends \Magento\Framework\App\Action\Action
{
    /** @var Magento\Quote\Api\CartRepositoryInterface  */
    private $cartRepository;
    /** @var Magento\Quote\Model\QuoteIdMaskFactory  */
    private $quoteIdMaskFactory;

    public function __construct(
        Context $context,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $phone = $this->getRequest()->getParam('phone');
        $quoteId = $this->getRequest()->getParam('quote_id');
        if ($phone && $quoteId) {
            $quote = $this->cartRepository->get($quoteId);
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setTelephone($phone);
            $shippingAddress->save();
            $quote->setInpostPhone($phone);
            $quote->save();
        }
    }
}