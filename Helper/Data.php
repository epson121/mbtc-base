<?php

namespace Mbtc\Base\Helper;

use BitWasp\BitcoinLib\BIP32;
use Endroid\QrCode\QrCode;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    private $rootDir;

    const XML_PAYMENT_BITCOIN_TEST = 'payment/bitcoin/testnet';
    const XML_PAYMENT_BITCOIN_XPUB_MAINNET = 'payment/bitcoin/extended_pubkey';
    const XML_PAYMENT_BITCOIN_XPUB_TESTNET = 'payment/bitcoin/extended_test_pubkey';
    const XML_PAYMENT_BITCOIN_AUTO_INVOICE = 'payment/bitcoin/auto_invoice';
    const XML_PAYMENT_BITCOIN_NUM_CONFIRMATIONS = 'payment/bitcoin/num_confirmations';
    const XML_PAYMENT_BITCOIN_DERIVATION_PATH = 'payment/bitcoin/derivation_path';
    const XML_PAYMENT_BITCOIN_TRANSACTION_LABEL = 'payment/bitcoin/transaction_label';
    const XML_PAYMENT_BITCOIN_RATE_PROVIDER = 'payment/bitcoin/rate_provider';
    const XML_PAYMENT_BITCOIN_RATES = 'payment/bitcoin/rates';
    const XML_PAYMENT_BITCOIN_EXPLORERS = 'payment/bitcoin/explorers';
    const XML_PAYMENT_BITCOIN_LATEST_BLOCK_HEIGHT = 'payment/bitcoin/latest_block_height';

    const CONVERSION_RATE = 1000;
    const SAVE_QR_IMAGES_PATH = 'qr';
    const PRECISION = 8;

    ### testnet
    ##      test
    ##      bunker estate toddler diesel split tape gate output genre slice obey adult
    ##      tpubD6NzVbkrYhZ4Y7zourYo7r4218a5eMs6ZLjkf8ycx44rvmxGMNcLAjh1YjZzToJdPPMbhiN8mQdWQThvecSUScnLkErYBnCxtmLfNqnx9cG
    ### mainnet
    ##      same
    ##      same
    ##      xpub661MyMwAqRbcGKoxTfZhuvieg1UReyN31i9TJavUc9JyBVHZH9mF2J2BJhVCwJMPbUph68rGEJoTiHDKFkbEZBL3ge7EWFYjJokFcrAzggd

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     */
    public function __construct(Context $context, Filesystem $filesystem)
    {
        $this->mediaDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
    public function isTestMode($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PAYMENT_BITCOIN_TEST, $scope, $scopeCode);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
    public function isAutoInvoiceEnabled($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PAYMENT_BITCOIN_AUTO_INVOICE, $scope, $scopeCode);
    }

    /**
     * @param $scope
     * @param null $scopeCode
     * @return bool
     */
    public function getNumConfirmations($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_NUM_CONFIRMATIONS, $scope, $scopeCode);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
    public function getExtendedPubkey($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        if ($this->isTestMode()) {
            return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_XPUB_TESTNET, $scope, $scopeCode);
        } else {
            return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_XPUB_MAINNET, $scope, $scopeCode);
        }
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return bool
     */
    public function getDerivationPath($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_DERIVATION_PATH, $scope, $scopeCode);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
//    public function getTransactionLabel($scope = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
//    {
//        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_TRANSACTION_LABEL, $scope, $scopeCode);
//    }

    /**
     * @return mixed
     */
    public function getRates()
    {
        $rates = unserialize($this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_RATES));
        return $rates ?: [];
    }


    public function getLatestBlockHeight()
    {
        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_LATEST_BLOCK_HEIGHT);
    }

    /**
     * @param $baseCurrencyCode
     * @param $baseTotal
     * @return float|null
     */
    public function convertRate($baseCurrencyCode, $baseTotal)
    {
        $rates = $this->getRates();
        $result = null;
        foreach($rates as $rate) {
            if ($rate['code'] == $baseCurrencyCode) {
                $result = round((float)$baseTotal / $rate['rate'], self::PRECISION);
                break;
            }
        }

        return $result ?: (1/self::CONVERSION_RATE);
    }

    /**
     * @param $number
     * @return string
     */
    public function derivePath($number)
    {
        $derivationPath = $this->getDerivationPath();
        return $derivationPath . $number;
    }

    /**
     * @param $path
     * @return bool|null|string
     * @throws \Exception
     */
    public function generateAddress($path)
    {

        $address = null;

        if (!$path) {
            return null;
        }

        try {
            $key = $this->getExtendedPubkey();
            $derivedKey = BIP32::build_key($key, $path);
            $address = BIP32::key_to_address($derivedKey[0]);
        } catch(LocalizedException $e) {

        }

        return $address;
    }


    public function generateTransactionMessage($id)
    {
        return urlencode(__('Payment for order: #%1', $id)->render());
    }


    public function generateQrCode($address, $amount, $message)
    {
        $qrCode = new QrCode();

        $text = $this->generateUriScheme($address, $amount, $message);

        $qrCode->setText($text)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
            ->setLabel('Address QR code')
            ->setLabelFontSize(8)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        $imageFilename = self::SAVE_QR_IMAGES_PATH . DIRECTORY_SEPARATOR . hash('sha256', sprintf('qr_%s.png', $address . rand()));


        if (!$this->mediaDir->isExist(self::SAVE_QR_IMAGES_PATH)) {
            $this->mediaDir->create(self::SAVE_QR_IMAGES_PATH);
        }

        $path = $this->mediaDir->getAbsolutePath($imageFilename);
        $qrCode->save($path);

        return $imageFilename;
    }

    private function generateUriScheme($address, $amount, $message)
    {
        $params = [
            'amount'    => $amount,
            'message'   => $message
        ];

//        if ($label = $this->getTransactionLabel()) {
//            $params['label'] = $label;
//        }

        $uri = "bitcoin:{$address}?" . http_build_query($params);

        return $uri;
    }

    /**
     * @return string
     */
    public function getRateServiceProvider()
    {
        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_RATE_PROVIDER);
    }

    /**
     * @return string
     */
    public function getBlockchainExplorerService()
    {
        return $this->scopeConfig->getValue(self::XML_PAYMENT_BITCOIN_EXPLORERS);
    }

    public function getRateInfo($baseCurrencyCode)
    {
        $rates = $this->getRates();

        foreach($rates as $rate) {
            if ($rate['code'] == $baseCurrencyCode) {
                return $rate;
            }
        }

        return null;
    }

}