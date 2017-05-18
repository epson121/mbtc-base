<?php
/**
 * Created by PhpStorm.
 * User: luka
 * Date: 03/02/17
 * Time: 12:01
 */

namespace Mbtc\Base\Model\Rate\Import;


use Magento\Framework\ObjectManagerInterface;
use Mbtc\Base\Model\Rate\Import\Config;
use Mbtc\Base\Model\Rate\Import\ImportInterface;

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
     * @return \Mbtc\Base\Model\Rate\Import\ImportInterface
     */
    public function create($serviceName, array $data = [])
    {
        $serviceClass = $this->_serviceConfig->getServiceClass($serviceName);
        if (!$serviceClass) {
            throw new \InvalidArgumentException("Rate import service '{$serviceName}' is not defined.");
        }
        $serviceInstance = $this->_objectManager->create($serviceClass, $data);
        if (!$serviceInstance instanceof ImportInterface) {
            throw new \UnexpectedValueException(
                "Class '{$serviceClass}' has to implement \\Mbtc\\Base\\Model\\Rate\\Import\\ImportInterface."
            );
        }
        return $serviceInstance;
    }

}