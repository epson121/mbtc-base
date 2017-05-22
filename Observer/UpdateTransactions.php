<?php

namespace Mbtc\Base\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mbtc\Base\Helper\Data;
use Mbtc\Base\Model\TransactionFactory;

class UpdateTransactions implements ObserverInterface {

    /**
     * @var Data
     */
    private $helper;

    private $transactionFactory;

    public function __construct(
        Data $helper,
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
        $transactionFactory = $this->transactionFactory->create();
        $orderId = $observer->getOrder()->getEntityId();
        $transaction = $transactionFactory->load($orderId, 'order_id');
        return $transaction->setStatus(\Mbtc\Base\Model\Transaction::STATUS_COMPLETE)->save();
    }

}