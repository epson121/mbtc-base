<?php

namespace Mbtc\Base\Model\ResourceModel;


use Magento\Sales\Model\ResourceModel\EntityAbstract;

class Transaction extends EntityAbstract
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mbtc_transactions', 'entity_id');
    }

    public function loadByParent($object)
    {
//        $connection = $this->getConnection();
//        $select = $connection->select()
//            ->from(['main_table' => $this->getMainTable()])
//            ->where('main_table.parent_id =?', $object->getParentId())
//            ->where('main_table.parent_type =?', $object->getParentType());
//
//        if ($data = $connection->fetchRow($select)) {
//            $object->setData($data);
//        }
//
//        $this->unserializeFields($object);
//        $this->_afterLoad($object);
//        return $object;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice | \Magento\Sales\Model\Order\CreditMemo $entity
     * @return boolean
     */
    public function exists($entity)
    {
//        $select = $this->getConnection()->select()->from(
//            $this->getMainTable(),
//            'entity_id'
//        )->where(
//            'parent_id = ?',
//            $entity->getId()
//        )->where(
//            'website_id',
//            $entity->getStore()->getWebsiteId()
//        );
//
//        return $this->getConnection()->fetchOne($select) ? true : false;
    }


}