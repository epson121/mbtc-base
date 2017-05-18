<?php

namespace Mbtc\Base\Model\Rate\Import;


class Config
{

    /**
     * @var array
     */
    private $_servicesConfig;

    /**
     * Validate format of services configuration array
     *
     * @param array $servicesConfig
     * @throws \InvalidArgumentException
     */
    public function __construct(array $servicesConfig)
    {
        foreach ($servicesConfig as $serviceName => $serviceInfo) {
            if (!is_string($serviceName) || empty($serviceName)) {
                throw new \InvalidArgumentException('Name for a bitcoin rate import service has to be specified.');
            }
            if (empty($serviceInfo['class'])) {
                throw new \InvalidArgumentException('Class for a bitcoin rate import service has to be specified.');
            }
            if (empty($serviceInfo['code'])) {
                throw new \InvalidArgumentException('code for a bitcoin rate import service has to be specified.');
            }
            if (empty($serviceInfo['label'])) {
                throw new \InvalidArgumentException('Label for a bitcoin rate import service has to be specified.');
            }
        }

        $this->_servicesConfig = $servicesConfig;
    }

    /**
     * Retrieve unique names of all available currency import services
     *
     * @return array
     */
    public function getAvailableServices()
    {
        return array_keys($this->_servicesConfig);
    }

    /**
     * Retrieve name of a class that corresponds to service name
     *
     * @param string $serviceName
     * @return string|null
     */
    public function getServiceClass($serviceName)
    {
        if (isset($this->_servicesConfig[$serviceName]['class'])) {
            return $this->_servicesConfig[$serviceName]['class'];
        }
        return null;
    }

    /**
     * Retrieve name of a class that corresponds to service name
     *
     * @param string $serviceName
     * @return string|null
     */
    public function getServiceCode($serviceName)
    {
        if (isset($this->_servicesConfig[$serviceName]['code'])) {
            return $this->_servicesConfig[$serviceName]['code'];
        }
        return null;
    }

    /**
     * Retrieve already translated label that corresponds to service name
     *
     * @param string $serviceName
     * @return \Magento\Framework\Phrase|null
     */
    public function getServiceLabel($serviceName)
    {
        if (isset($this->_servicesConfig[$serviceName]['label'])) {
            return __($this->_servicesConfig[$serviceName]['label']);
        }
        return null;
    }

}