<?php

namespace Mbtc\Base\Model\Blockchain\Explorer;


use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\OrderRepository;
use Mbtc\Base\Helper\Data;

abstract class ExplorerAbstract {

    public $helper;

    public $confirmations;

    public $blockConfirmed;

    public $txId;

    public $isConfirmed;


    public function checkConfirmations($txId, $address, $amount)
    {
     return null;
    }

    /**
     * Get service response
     *
     * @param string $url
     * @param int $retry
     * @return array
     */
    public function getServiceResponse($url, $retry = 0)
    {
        /** @var \Magento\Framework\HTTP\ZendClient $httpClient */
        $httpClient = $this->httpClientFactory->create();
        $response = [];

        try {
            $jsonResponse = $httpClient->setUri(
                $url
            )->request(
                'GET'
            )->getBody();

            $response = json_decode($jsonResponse, true);
        } catch (\Exception $e) {
            if ($retry == 0) {
                $response = $this->getServiceResponse($url, 1);
            }
        }
        return $response;
    }


    public function isTestnetAvailable()
    {
        return false;
    }

    public function fetchLatestBlockHeight()
    {
        return false;
    }

    public function getTxId()
    {
        return $this->txId;
    }

    public function getConfirmations()
    {
        return $this->confirmations;
    }

    public function getBlockConfirmed()
    {
        return $this->blockConfirmed;
    }

    public function setConfirmations($confirmations)
    {
        $this->confirmations = $confirmations;
    }

    public function setBlockConfirmed($blockConfirmed)
    {
        $this->blockConfirmed = $blockConfirmed;
    }

    public function setTxId($txId)
    {
        $this->txId = $txId;
    }

    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;
    }

    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

}