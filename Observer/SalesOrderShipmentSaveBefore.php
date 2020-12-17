<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Redbox\Shipping\Api\Checkout\AddressRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class SalesOrderShipmentSaveBefore implements ObserverInterface
{

    private $adapter;
    private $request;
    private $addressRepository;
    private $quoteFactory;
    private $machineResource;
    private $machine;
    private $helper;
    private $objectManager;
    private $trackFactory;
    private $filesystem;
    private $invoiceService;
    private $transaction;
    private $scopeConfig;
    /** @var Magento\Framework\Filesystem\DriverInterface */
    private $driver;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Redbox\Shipping\Model\ResourceModel\Machine $machineResource,
        \Redbox\Shipping\Model\Machine $machine,
        \Redbox\Shipping\Helper\Lockers $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Inpost_Api_Client $client
    ) {
        $this->adapter = $client;
        $this->helper = $helper;
        $this->filesystem = $filesystem;
        $this->objectManager = $objectManager;
        $this->invoiceService = $invoiceService;
        $this->request = $request;
        $this->addressRepository = $addressRepository;
        $this->quoteFactory = $quoteFactory;
        $this->machineResource = $machineResource;
        $this->machine = $machine;
        $this->trackFactory = $trackFactory;
        $this->transaction = $transaction;
        $this->scopeConfig = $scopeConfig;
        $this->driver = $driver;
    }

    public function execute(Observer $observer)
    {
        $shipment = $observer->getShipment();
        $order = $shipment->getOrder();
        if ($order->getShippingMethod() == 'redbox_shipping' && $this->helper->isActive()) {
            if ($order->getInvoiceCollection()->getSize() == 0) {
                if ($order->canInvoice()) {
                    $invoice = $this->invoiceService->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                    $transactionSave = $this->transaction->addObject(
                        $invoice
                    )->addObject(
                        $invoice->getOrder()
                    );
                    $transactionSave->save();
                }
            }
        }
    }
}
