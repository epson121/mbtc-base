<?php

namespace Mbtc\Base\Cron;
use Mbtc\Base\Helper\Data;
use Mbtc\Base\Model\Rate\Import\Config;
use Mbtc\Base\Model\Rate\Import\Factory;

class UpdateExchangeRate {

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
     * @param Config $config
     * @param Factory $factory
     * @param Data $helper
     */
    public function __construct(
        Config $config,
        Factory $factory,
        Data $helper
    ) {
        $this->config = $config;
        $this->factory = $factory;
        $this->helper = $helper;
    }

    /**
     *
     */
    public function execute()
    {
        if ($services = $this->config->getAvailableServices()) {
            $selectedService = $this->helper->getRateServiceProvider();
            foreach ($services as $service) {
                if ($service == $selectedService) {
                    $service = $this->factory->create($service);
                    $service->importRates();
                    break;
                }
            }
        }
    }

}