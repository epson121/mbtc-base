<?php

namespace Mbtc\Base\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Transaction extends AbstractModel
{

    const STATUS_PENDING = '0';
    const STATUS_PROCESSING = '1';
    const STATUS_COMPLETE = '2';
    const STATUS_CANCELED = '3';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     * @internal param SequenceFactory $sequenceFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_logger = $context->getLogger();
        $this->_parent = null;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_construct();
    }

    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Mbtc\Base\Model\ResourceModel\Transaction');
    }


    public function update($data)
    {

    }

}