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

class Ced_CsCmsPage_Model_Mysql4_Cmspage extends Mage_Core_Model_Mysql4_Abstract
{
	 /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Initialize resource model
     */
	protected function _construct()
	{
		/**
		 * Initialize resource
		 *
		 */
		$this->_init('cscmspage/cmspage','page_id');
	}
	/**
     * Process page data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent datetime data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value.
         */
    	
        /*foreach (array('custom_theme_from', 'custom_theme_to') as $dataKey) {
            if (!$object->getData($dataKey)) {
                $object->setData($dataKey, new Zend_Db_Expr('NULL'));
            }
        }*/
		
        if (!$this->getIsUniquePageToStores($object)) {
			Mage::throwException(Mage::helper('cscmspage')->__('A page URL key for specified store already exists.'));
        }

        if (!$this->isValidPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cscmspage')->__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPageIdentifier($object)) {
            Mage::throwException(Mage::helper('cscmspage')->__('The page URL key cannot consist only of numbers.'));
        }

        if (! $object->getId()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }

    /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     */
    /*protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('page_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('cscmspage/vendorcms'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray['page_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('cscmspage/vendorcms'), $storeArray);
        }

        return parent::_afterSave($object);
    } */

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
    	if (!is_numeric($value)) {
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
    	$select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cscmspage/vendorcms'))
            ->where('page_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
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
		
        $storeId = $object->getStoreId();
        if ($storeId) {
            $select->join(
                        array('cps' => $this->getTable('cscmspage/vendorcms')),
                        $this->getMainTable().'.page_id = `cps`.page_id'
                    )
                    ->where('is_active=1 AND `cps`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * Check for unique of identifier of page to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->join(array('cps' => $this->getTable('cscmspage/vendorcms')), $this->getMainTable().'.page_id = `cps`.page_id')
                ->where($this->getMainTable().'.identifier = ?', $object->getData('identifier'));
        if ($object->getId()) {
            $select->where($this->getMainTable().'.page_id <> ?',$object->getId());
        }
        $stores = (array)$object->getData('stores');
        if (Mage::app()->isSingleStoreMode()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        }
        $select->where('`cps`.store_id IN (?)', $stores);
		
		if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether page identifier is numeric
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     *  @date     Wed Mar 26 18:12:28 EET 2008
     */
    protected function isNumericPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($identifier, $storeId)
    {	
    	if(Mage::helper('cscmspage')->getVendorId()>0){
    		//$vid = Mage::helper('cscmspage')->getVendorId();
    		$select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'page_id')
            ->join(
                array('cps' => $this->getTable('cscmspage/vendorcms')),
                'main_table.page_id = `cps`.page_id'
            )
            ->where('main_table.identifier=?', $identifier)
            ->where('main_table.is_active=1 AND `cps`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
            ->order('store_id DESC');
           
    	}else{
    		$select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'page_id')
            ->join(
                array('cps' => $this->getTable('cscmspage/vendorcms')),
                'main_table.page_id = `cps`.page_id'
            )
            ->where('main_table.identifier=?', $identifier)
            //->where('cps.vendor_id='.$vid )
            ->where('main_table.is_active=1 AND main_table.is_approve=1 AND `cps`.store_id IN (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
            ->order('store_id DESC');
    		
    	}
    	
		return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getCmsPageTitleByIdentifier($identifier)
    {
    	$select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $joinExpr = $this->_getReadAdapter()->quoteInto(
            'main_table.page_id = cps.page_id AND (cps.store_id = ' . Mage_Core_Model_App::ADMIN_STORE_ID . ' OR cps.store_id = ?)', $this->getStore()->getId()
        );
        $select->from(array('main_table' => $this->getMainTable()), 'title')
        ->joinLeft(array('cps' => $this->getTable('cscmspage/vendorcms')), $joinExpr ,array())
            ->where('main_table.identifier = ?', $identifier)
            ->order('cps.store_id DESC');
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageTitleById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'title')
            ->where('main_table.page_id = ?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getCmsPageIdentifierById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'identifier')
            ->where('main_table.page_id = ?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
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
            ->from($this->getTable('cscmspage/vendorcms'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }
}