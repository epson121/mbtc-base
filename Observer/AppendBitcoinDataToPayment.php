<?php

namespace Mbtc\Base\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mbtc\Base\Model\TransactionFactory;
use Mbtc\Base\Model\Ui\ConfigProvider;

class AppendBitcoinDataToPayment implements ObserverInterface {

    /**
     * @var \Mbtc\Base\Helper\Data
     */
    private $helper;

    /**
     * @var ConfirmationsFactory
     */
    private $transactionFactory;

    public function __construct(
        \Mbtc\Base\Helper\Data $helper,
        TransactionFactory $transactionFactory
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getDataObject();

        $paymentMethod = $order->getPayment()->getMethod();

        $payment = $order->getPayment();
        $additionalInformation = $payment->getAdditionalInformation();

        if ($paymentMethod === ConfigProvider::CODE && !isset($additionalInformation['bitcoin_address'])) {
            $additionalInformation = $this->appendAdditionalInformation($order, $additionalInformation);

            $this->saveTransactionData($order, $additionalInformation);

            $payment->setAdditionalInformation($additionalInformation);
        }

    }

    /**
     * @param $order
     * @param $additionalInformation
     * @return mixed
     */
    public function appendAdditionalInformation($order, $additionalInformation)
    {
        $entityId = (int)$order->getIncrementId();

        $path = $this->helper->derivePath($entityId);
        $address = $this->helper->generateAddress($path);
        $baseCurrencyCode = $order->getStore()->getBaseCurrencyCode();

        $amount = $this->helper->convertRate(
            $baseCurrencyCode,
            $order->getBaseTotalDue()
        );

        $rateInfo = $this->helper->getRateInfo($baseCurrencyCode);

        $img = $this->helper->generateQrCode(
            $address,
            $amount,
            $this->helper->generateTransactionMessage($entityId)
        );

        $additionalInformation['bitcoin_address'] = $address;
        $additionalInformation['bitcoin_derived_path'] = $path;
        $additionalInformation['bitcoin_qr_path'] = $img;
        $additionalInformation['bitcoin_amount'] = $amount;
        $additionalInformation['base_rate_info'] = $rateInfo;

        return $additionalInformation;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $additionalInformation
     */
    public function saveTransactionData($order, $additionalInformation)
    {
        /** @var \Mbtc\Base\Model\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $transaction->setOrderId($order->getEntityId());
        $transaction->setAddress($additionalInformation['bitcoin_address']);
        $transaction->save();
    }


}