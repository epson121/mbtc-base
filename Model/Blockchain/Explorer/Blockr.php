<?php

namespace Mbtc\Base\Model\Blockchain\Explorer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Mbtc\Base\Helper\Data;
use Mbtc\Base\Model\Transaction;


class Blockr extends ExplorerAbstract
{

    const ADDRESS_INFO_URL = 'http://tbtc.blockr.io/api/v1/address/info/';
    const LAST_BLOCK_INFO_URL = 'http://tbtc.blockr.io/api/v1/block/info/last';
    const TRANSACTION_INFO_URL = 'http://tbtc.blockr.io/api/v1/tx/info/';

    public $httpClientFactory;

    public function __construct(
        ZendClientFactory $httpClientFactory
    )
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    public function isTestnetAvailable()
    {
        return true;
    }

    /**
     * @param $txId
     * @param $address
     * @param $amount
     * @return null|void
     * @internal param $order
     * @internal param Transaction $transaction
     */
    public function checkConfirmations($txId, $address, $amount, $payment)
    {
        $txInfo = null;

        if ($txId) {
            $txInfo = $this->getTransactionInfo($txId);
        } else {
            $addressInfo = $this->getAddressInfo($address, $amount);
            if ($addressInfo) {
                $txInfo = $addressInfo['first_tx'];
            }
        }

        if ($txInfo) {
            $blockNumber = isset($txInfo['block']) ? $txInfo['block'] : $txInfo['block_nb'];
            $this->setConfirmations($txInfo['confirmations']);
            $this->setBlockConfirmed($blockNumber);
            $this->setTxId($txInfo['tx']);
            $this->setIsConfirmed(true);
        }

        return $this->getIsConfirmed();
    }

    public function fetchData($url)
    {
        $data = null;
        $response = $this->getServiceResponse($url);

        if ($response['code'] == 200 && $response['status'] == 'success') {
            $data = $response['data'];
        }

        return $data;
    }

    public function getTransactionInfo($txId)
    {

        $transactionData = $this->fetchData(self::TRANSACTION_INFO_URL . $txId);

        return $transactionData ? $transactionData : null;

    }

    public function fetchLatestBlockHeight()
    {
        $response = $this->fetchData(self::LAST_BLOCK_INFO_URL);

        if (isset($response['nb'])) {
            return $response['nb'];
        }

        return null;
    }

    public function getAddressInfo($address, $amount)
    {

        $addressData = $this->fetchData(self::ADDRESS_INFO_URL . $address);

        if ($addressData && $this->validateAddressData($addressData, $amount)) {
            return $addressData;
        }

        return null;

    }

    public function validateAddressData($addressData, $amount)
    {

        return $addressData['is_valid']
                && $addressData['nb_txs'] > 0
                && $addressData['totalreceived'] >= $amount;

    }

}