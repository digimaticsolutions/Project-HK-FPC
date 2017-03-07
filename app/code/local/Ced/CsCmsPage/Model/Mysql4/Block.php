<?php

/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsCmsPage_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Initialize resource
	 *
	 */
	protected function _construct()
	{
		
		$this->_init('cscmspage/block','block_id');
	}
	
	/**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
    	if (!$this->getIsUniqueBlockToStores($object)) {
			Mage::throwException(Mage::helper('cscmspage')->__('A block identifier with the same properties already exists in the selected store.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    /*protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('block_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('cscmspage/vendorblock'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['block_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('cscmspage/vendorblock'), $storeArray);
        }

        return parent::_afterSave($object);
    } */

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {

        if (!intval($value) && is_string($value)) {
            $field = 'identifier';
        }
        return parent::load($object, $value, $field);
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('cscmspage/vendorblock'))
                ->where('block_id = ?', $object->getId());

            if ($data = $this->_getReadAdapter()->fetchAll($select)) {
                $storesArray = array();
                foreach ($data as $row) {
                    $storesArray[] = $row['store_id'];
                }
                $object->setData('store_id', $storesArray);
            }
        }

        return parent::_afterLoad($object);
    }

        /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
    	$select = parent::_getLoadSelect($field, $value, $object);
    	if(Mage::helper('cscmspage')->getVendorId()>0){
    		$vid = Mage::helper('cscmspage')->getVendorId();
	    	if ($object->getStoreId()) {
	            $select->join(array('cbs' => $this->getTable('cscmspage/vendorblock')), $this->getMainTable().'.block_id = cbs.block_id')
	                    ->where('is_active=1 AND cbs.store_id in (0, ?) ', $object->getStoreId())
	                    ->order('store_id DESC')
	                    ->limit(1);
	        }
    	}else{
	    	if ($object->getStoreId()) {
	            $select->join(array('cbs' => $this->getTable('cscmspage/vendorblock')), $this->getMainTable().'.block_id = cbs.block_id')
	                    ->where('is_active=1 AND is_approve=1 AND cbs.store_id in (0, ?) ', $object->getStoreId())
	                    ->order('store_id DESC')
	                    ->limit(1);
	        }
    	}

        
        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueBlockToStores(Mage_Core_Model_Abstract $object)
    {
    	
    	$select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('cbs' => $this->getTable('cscmspage/vendorblock')), $this->getMainTable().'.block_id = `cbs`.block_id')
                ->where($this->getMainTable().'.identifier = ?', $object->getData('identifier'));
        
        if ($object->getId()) {
            $select->where($this->getMainTable().'.block_id <> ?',$object->getId());
        }
    	//$select->where('`cbs`.store_id IN (?)', (array)$object->getData('stores'));
    	if(isset($_POST['vcmspage']['store'])){
        	$select->where('`cbs`.store_id IN (?)', $_POST['vcmspage']['store']);
    	}
		
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
            ->from($this->getTable('cscmspage/vendorblock'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }
    
}