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
 * Core helper Product
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsCommission_Helper_Product extends Mage_Core_Helper_Abstract
{

    const CONFIG_DB_PRODUCT_USAGE_OPTIONS = 'ced_vpayments/general/commission_pt';

	const OPTION_PRODUCT_PREFIX = '';
	
	const OPTION_PRODUCT_PREFIX_SEPARATOR = '';

	/**
	 *  @param string $types 
	 *  @return int Product Type Code
	 */
	public function getCodeValue($types = 'all'){
		return self::OPTION_PRODUCT_PREFIX.self::OPTION_PRODUCT_PREFIX_SEPARATOR.$types;
	}
	
	/**
	 *  @param array $value
	 *  @return Serialized Options
	 */
    public function getSerializedOptions($value)
    {
		$uniqueValues = array();
		if(is_array($value)){
			$cnt = 0;
			foreach($value as $key=>$val){
				if(!is_array($val)) {
					continue;
				}
				if(isset($val['method']) && !in_array($val['method'],array('fixed','percentage'))) {
					$val['method'] = 'fixed';
				}
				switch($val['method']) {
					case "fixed" : $val['fee'] = round($val['fee'],2); break;
					case "percentage" : $val['fee'] = min((int)$val['fee'],100); break;
				}
				if(isset($val['priority']) && !is_numeric($val['priority'])) {
					if(strlen($val['priority']) > 0) {
						$val['priority'] = (int)$val['priority'];
					} else {
						$val['priority'] = $cnt;
					}
					/* Mage::throwException("[".(int)$val['priority']."]==".print_r($val,true)."==[".$val['priority']."]"); */
				}
				/* Mage::throwException("{".(int)$val['priority']."}==".print_r($val,true)."=={".$val['priority']."}"); */
				if(!isset($uniqueValues[$this->getCodeValue($val['types'])])) {
					$uniqueValues[$this->getCodeValue($val['types'])] = $val;
				} elseif (isset($uniqueValues[$this->getCodeValue($val['types'])]) && isset($uniqueValues[$this->getCodeValue($val['types'])]['priority']) && isset($val['priority']) && (int)$val['priority'] < (int)$uniqueValues[$this->getCodeValue($val['types'])]['priority']) {
					$uniqueValues[$this->getCodeValue($val['types'])] = $val;
				}

				$cnt++;
			}
			/* Mage::throwException(print_r($uniqueValues,true)); */
		}
        return serialize($uniqueValues);
    }

    /**
     *  @param int $storeId
     *  @return UnSerialized Options
     */
    public function getUnserializedOptions($storeId = null)
    {
        $value = Mage::getStoreConfig(self::CONFIG_DB_PRODUCT_USAGE_OPTIONS, $storeId);
		/*
			$id = 0;
			if(Mage::registry('current_order_vendor')) $id = Mage::registry('current_order_vendor'); 
			Mage::log($id.'=='.self::CONFIG_DB_PRODUCT_USAGE_OPTIONS."--value: ".print_r($value, true).' ',null,'cscommission_product.log'); 
		*/
		$arr = @unserialize($value);
		/*
			Mage::log($id.'=='.self::CONFIG_DB_PRODUCT_USAGE_OPTIONS."--array: ".print_r($arr, true).' ',null,'cscommission_product.log');
		*/
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

        return $arr;
    }

    /**
     *  @param int $storeId
     *  @return array $options
     */
    public function getOptions($storeId = null)
    {
        $rawOptions = $this->getUnserializedOptions($storeId);

        $options = array();
		if(count($rawOptions) > 0) {
			foreach ($rawOptions as $option) {
				$options[$option['code']] = $option;
			}
		}
        return $options;
    }

    /**
     *  @param int $storeId
     *  @return array $options
     */
    public function getDefaultOption($storeId = null)
    {
        $options = $this->getUnserializedOptions($storeId);

        //last one set as default
        foreach ($options as $k=>$val) {
            if($val['default'] == 1) {
                return $val;
            }
        }

       return array();
    }
}
