<?php

namespace Mbtc\Base\Model\Blockchain\Explorer;


use Magento\Framework\ObjectManagerInterface;
use Mbtc\Base\Model\Blockchain\Explorer\Config;
use Mbtc\Base\Model\Blockchain\Explorer\ExplorerAbstract;

class Factory {

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;


    protected $_serviceConfig;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $serviceConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $serviceConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_serviceConfig = $serviceConfig;
    }

    /**
     * Create new import object
     *
     * @param string $serviceName
     * @param array $data
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @return \Mbtc\Base\Model\Blockchain\Explorer\ImportInterface
     */
    public function create($serviceName, array $data = [])
    {
        $serviceClass = $this->_serviceConfig->getServiceClass($serviceName);
        if (!$serviceClass) {
            throw new \InvalidArgumentException("Blockchain explorer service '{$serviceName}' is not defined.");
        }
        $serviceInstance = $this->_objectManager->create($serviceClass, $data);
        if (!$serviceInstance instanceof ExplorerAbstract) {
            throw new \UnexpectedValueException(
                "Class '{$serviceClass}' has to implement Blockchain explorer interface."
            );
        }
        return $serviceInstance;
    }

}