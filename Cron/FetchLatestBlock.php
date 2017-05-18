<?php

namespace Mbtc\Base\Cron;

use Magento\Framework\App\Config\ScopePool;
use Magento\Framework\Cache\FrontendInterface;
use Mbtc\Base\Helper\Data;
use Mbtc\Base\Model\Blockchain\Explorer\Config;
use Mbtc\Base\Model\Blockchain\Explorer\Factory;
use Mbtc\Base\Model\ResourceModel\Transaction\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


class FetchLatestBlock {

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


    private $configResource;

    /**
     * @param Config $config
     * @param Factory $factory
     * @param Data $helper
     * @param CollectionFactory $transactionCollectionFactory
     * @param \Magento\Config\Model\ResourceModel\Config $configResource
     * @param FrontendInterface $cache
     */
    public function __construct(
        Config $config,
        Factory $factory,
        Data $helper,
        CollectionFactory $transactionCollectionFactory,
        \Magento\Config\Model\ResourceModel\Config $configResource,
        FrontendInterface $cache
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->helper = $helper;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->configResource = $configResource;
        $this->cache = $cache;
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

        $blockHeight = $service->fetchLatestBlockHeight();

        if ($blockHeight) {
            $this->configResource->saveConfig(Data::XML_PAYMENT_BITCOIN_LATEST_BLOCK_HEIGHT, $blockHeight, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            //  todo optimize this somehow
            $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [ScopePool::CACHE_TAG]);
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

}