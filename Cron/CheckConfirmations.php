<?php

namespace Mbtc\Base\Cron;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Invoice;
use Mbtc\Base\Helper\Data;
use Mbtc\Base\Model\Blockchain\Explorer\Config;
use Mbtc\Base\Model\Blockchain\Explorer\Factory;
use Mbtc\Base\Model\ResourceModel\Transaction\CollectionFactory;
use Mbtc\Base\Model\Transaction;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class CheckConfirmations {


    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @param Config $config
     * @param Factory $factory
     * @param Data $helper
     * @param CollectionFactory $transactionCollectionFactory
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param InvoiceSender $invoiceSender
     */
    public function __construct(
        Config $config,
        Factory $factory,
        Data $helper,
        CollectionFactory $transactionCollectionFactory,
        OrderRepository $orderRepository,
//        SearchCriteriaBuilder $searchCriteriaBuilder,
        InvoiceSender $invoiceSender
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->helper = $helper;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->orderRepository = $orderRepository;
//        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectManager = ObjectManager::getInstance();
        $this->invoiceSender = $invoiceSender;
    }

    /**
     *
     */
    public function execute()
    {

        /** @var \Mbtc\Base\Model\Blockchain\Explorer\ExplorerAbstract $service */
        $service = $this->getService();

        if (!$service) {
            return;
        }

        $transactions = $this->getTransactions();
        $a = $this->helper->isAutoInvoiceEnabled();

        /** @var \Mbtc\Base\Model\Transaction $transaction */
        foreach ($transactions as $transaction) {

            $order = $this->orderRepository->get($transaction->getOrderId());

            $transactionSave = $this->objectManager->create('Magento\Framework\DB\Transaction');

            /** @var  $payment \Magento\Sales\Model\Order\Payment*/
            $payment = $order->getPayment();
            $address = $payment->getAdditionalInformation('bitcoin_address');
            $amount = $payment->getAdditionalInformation('bitcoin_amount');

            $waitConfirmations = $this->helper->getNumConfirmations();

            $valid = $service->checkConfirmations($transaction->getTxId(), $address, $amount);


            if ($valid) {

                $payment->setAdditionalInformation('bitcoin_confirmations', $service->getConfirmations());
                $payment->setAdditionalInformation('bitcoin_block_confirmed', $service->getBlockConfirmed());

                if ($service->getConfirmations() >= $waitConfirmations) {

                    $transaction->setStatus(Transaction::STATUS_SUCCESS);

                    if ($this->helper->isAutoInvoiceEnabled()) {
                        /** @var Invoice $invoice */
                        $invoice = $order->prepareInvoice();

                        if (!$invoice) {
                            throw new LocalizedException(__('We can\'t save the invoice right now.'));
                        }

                        if (!$invoice->getTotalQty()) {
                            throw new LocalizedException(
                                __('You can\'t create an invoice without products.')
                            );
                        }

                        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE)->register();
                        $invoice->register();

                        $invoice->getOrder()->setCustomerNoteNotify(false);
                        $invoice->getOrder()->setIsInProcess(true);

                        $transactionSave->addObject(
                            $invoice
                        )->addObject(
                            $invoice->getOrder()
                        );

                        // todo check setting
                        $this->invoiceSender->send($invoice);
                    }
                } else {
                    $transaction->setTxId($service->getTxId());
                    $transaction->setStatus(Transaction::STATUS_PROCESSING);
                }

                $transactionSave->addObject(
                    $payment
                )->addObject(
                    $transaction
                );

                $transactionSave->save();

            }
        }

    }

    public function getService()
    {
        if ($services = $this->config->getAvailableServices()) {
            if ($this->helper->isTestMode()) {
                return $this->factory->create('blockr');
            }
            $selectedExplorer = $this->helper->getBlockchainExplorerService();
            foreach ($services as $service) {
                if ($service == $selectedExplorer) {
                    $service = $this->factory->create($service);
                    return $service;
                }
            }
        }

        return null;
    }

    /**
     * @return $this
     */
    public function getTransactions()
    {
        /** @var \Mbtc\Base\Model\ResourceModel\Transaction\Collection $collection */
        $collection = $this->transactionCollectionFactory->create();
        return $collection->getPendingTransactions();
    }


}