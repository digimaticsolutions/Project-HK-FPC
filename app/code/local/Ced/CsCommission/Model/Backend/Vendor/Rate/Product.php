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
  * @package     Ced_CsCommission
  * @author  	 CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
 
/**
 * Backend for serialized array data
 *
 */
class Ced_CsCommission_Model_Backend_Vendor_Rate_Product extends Mage_Core_Model_Config_Data
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $arr   = @unserialize($value);

        if(!is_array($arr)) return '';

        $sortOrder = array();
		$cnt = 1;
        foreach ($arr as $k=>$val) {
            if(!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
            $sortOrder[$k] = isset($val['priority'])?$val['priority']:$cnt++;
        }

        //sort by priority
        array_multisort($sortOrder, SORT_ASC, $arr);
        $this->setValue($arr);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
		
        $value = Mage::helper('cscommission/product')->getSerializedOptions($value);
        $this->setValue($value);
		return parent::_beforeSave();
    }
	
	/**
     * after save
     */
	/* protected function _afterSave(){
		$value 	= $this->getValue();
		$arr 	= @unserialize($value);
		$parr	= array();
		$parr 	= Mage::helper('cscommission/product')->getOptions();

		return parent::_afterSave();
	} */
}