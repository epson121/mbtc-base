<?php

namespace Mbtc\Base\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mbtc\Base\Model\Transaction;

class Collection extends AbstractCollection {

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mbtc\Base\Model\Transaction', 'Mbtc\Base\Model\ResourceModel\Transaction');
    }

    /**
     * @return $this
     */
    public function getPendingTransactions() {
        $this->addFieldToSelect('*')
            ->addFieldToFilter('status', ['in' => [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING]]);

        return $this;
    }

    public function getTransactionByOrderId($orderId) {
        $this->addFieldToSelect('*')
                ->addFieldToFilter('order_id', $orderId)->lo;

        return $this;
    }

}