<?php

namespace Mbtc\Base\Model\Rate\Import;
use Magento\Directory\Model\Currency\Import\AbstractImport;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Mbtc\Base\Model\Rate\Import\ImportInterface;


/**
 * Currency rate import model (From https://bitpay.com/api/rates)
 */
class Bitpay implements ImportInterface
{
    /**
     * @var string
     */
    const URL = 'https://bitpay.com/rates';

    /**
     * Http Client Factory
     *
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Core scope config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var Config
     */
    private $configResource;


    /**
     * Initialize dependencies
     *
     * @param CurrencyFactory $currencyFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ZendClientFactory $httpClientFactory
     * @param Config $configResource
     * @internal param Config $config
     */
    public function __construct(
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        ZendClientFactory $httpClientFactory,
        \Magento\Config\Model\ResourceModel\Config $configResource
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->scopeConfig = $scopeConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->configResource = $configResource;
    }

    public function importRates()
    {
        $data = $this->fetchRates();
        $this->_saveRates($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRates()
    {
        return $this->getServiceResponse(self::URL);
    }

    /**
     * Get Fixer.io service response
     *
     * @param string $url
     * @param int $retry
     * @return array
     */
    private function getServiceResponse($url, $retry = 0)
    {
        /** @var \Magento\Framework\HTTP\ZendClient $httpClient */
        $httpClient = $this->httpClientFactory->create();
        $response = [];

        try {
            $jsonResponse = $httpClient->setUri(
                $url
            )->setConfig(
                [
                    'timeout' => 10,
                ]
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

    public function _saveRates($rates)
    {
        $result = [];
        $defaultCurrencies = $this->_getDefaultCurrencyCodes();
        foreach ($defaultCurrencies as $currency) {

            foreach ($rates['data'] as $rate) {
                if ($rate['code'] == $currency) {
                    $result[] = $rate;
                    break;
                }
            }
        }

        $this->configResource->saveConfig(\Mbtc\Base\Helper\Data::XML_PAYMENT_BITCOIN_RATES, serialize($result), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);

        return $this;
    }

    /**
     * Retrieve default currency codes
     *
     * @return array
     */
    protected function _getDefaultCurrencyCodes()
    {
        return $this->currencyFactory->create()->getConfigBaseCurrencies();
    }

}
