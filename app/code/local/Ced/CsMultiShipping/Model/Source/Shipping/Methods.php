<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsMultiShipping
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsMultiShipping_Model_Source_Shipping_Methods extends Ced_CsMarketplace_Model_System_Config_Source_Abstract
{

	const XML_PATH_CED_CSMULTISHIPPING_SHIPPING_METHODS = 'global/ced_csmultishipping/shipping/methods';
    
	/**
	 * Retrieve rates data form config.xml
	 * @return array
	 */
	 
	 public static function getMethods() {

		$rates = Mage::app()->getConfig()->getNode(self::XML_PATH_CED_CSMULTISHIPPING_SHIPPING_METHODS);
        $rates = json_decode(json_encode($rates),true);
		
        $allowedmethods=array();
        if(is_array($rates) && count($rates)>0){
	        foreach ($rates as $code => $method){
	        	if(Mage::getStoreConfig($method['config_path'],Mage::app()->getStore()->getId()))
	        		$allowedmethods[$code]=$rates[$code];
	        }
        }
        return $allowedmethods;
	 }
	/**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$methods = array_keys(self::getMethods());
		$options = array();
		foreach($methods as $method) {
			$method = strtolower(trim($method));
			$options[] = array('value'=>$method,'label'=>Mage::helper('csmultishipping')->__(ucfirst($method)));
		}
		return $options;
    }

}