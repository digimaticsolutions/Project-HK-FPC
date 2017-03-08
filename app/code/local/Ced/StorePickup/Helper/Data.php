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
* @package     Ced_StorePickup
* @author      CedCommerce Core Team <connect@cedcommerce.com >
* @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/  
Class Ced_StorePickup_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * redirect to 404 page
	 */
	public function redirect404($frontController)
	{
		$frontController->getResponse()
		->setHeader('HTTP/1.1','404 Not Found');
		$frontController->getResponse()
		->setHeader('Status','404 File not found');
	
		$pageId = Mage::getStoreConfig('web/default/cms_no_route');
		if (!Mage::helper('cms/page')->renderPage($frontController, $pageId)) {
			$frontController->_forward('defaultNoRoute');
		}
	}
	
	public function getCedCommerceExtensions($asString = false,$productName = false) {
		if($asString) {
			$cedCommerceModules = '';
		} else {
			$cedCommerceModules = array();
		}
		$allModules = Mage::app()->getConfig()->getNode(Ced_StorePickup_Model_Feed::XML_PATH_INSTALLATED_MODULES);
		$allModules = json_decode(json_encode($allModules),true);
		foreach($allModules as $name=>$module) {
			$name = trim($name);
			if(preg_match('/ced_/i',$name) && isset($module['release_version']) && !preg_match('/ced_csvendorpanel/i',$name) && !preg_match('/ced_cstransaction/i',$name)) {
	
				if($asString) {
					$cedCommerceModules .= $name.':'.trim($module['release_version']).'~';
				} else {
					if($productName){
						$cedCommerceModules[$name]['release_version'] = trim($module['release_version']);
						$cedCommerceModules[$name]['parent_product_name'] = (isset($module['parent_product_name']) && strlen($module['parent_product_name']) > 0) ? $module['parent_product_name'] : trim($name);
					} else {
						$cedCommerceModules[$name] = trim($module['release_version']);
					}
						
				}
			}
		}
		if($asString) trim($cedCommerceModules,'~');
		return $cedCommerceModules;
	}
	public function getEnvironmentInformation () {
		$info = array();
		$info['domain_name'] = Mage::getBaseUrl();
		$info['framework'] = 'magento';
		$info['edition'] = 'default';
		if(method_exists('Mage','getEdition')) $info['edition'] = Mage::getEdition();
		$info['version'] = Mage::getVersion();
		$info['php_version'] = phpversion();
		$info['feed_types'] = Mage::getStoreConfig(Ced_StorePickup_Model_Feed::XML_FEED_TYPES);
		$info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_general/name');
		if(strlen($info['admin_name']) == 0) $info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_sales/name');
		$info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_general/email');
		if(strlen($info['admin_email']) == 0) $info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_sales/email');
		$info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);
	
		return $info;
	}
	
	public function addParams($url = '', $params = array(), $urlencode = true) {
		if(count($params)>0){
			foreach($params as $key=>$value){
				if(parse_url($url, PHP_URL_QUERY)) {
					if($urlencode)
						$url .= '&'.$key.'='.$this->prepareParams($value);
					else
						$url .= '&'.$key.'='.$value;
				} else {
					if($urlencode)
						$url .= '?'.$key.'='.$this->prepareParams($value);
					else
						$url .= '?'.$key.'='.$value;
				}
			}
		}
		return $url;
	}
	public function prepareParams($data){
		if(!is_array($data) && strlen($data)){
			return urlencode($data);
		}
		if($data && is_array($data) && count($data)>0){
			foreach($data as $key=>$value){
				$data[$key] = urlencode($value);
			}
			return $data;
		}
		return false;
	}
	
	/**
	 * Retrieve admin interest in current feed type
	 *
	 * @param SimpleXMLElement $item
	 * @return boolean $isAllowed
	 */
	public function isAllowedFeedType(SimpleXMLElement $item) {
		$isAllowed = false;
		if(is_array($this->_allowedFeedType) && count($this->_allowedFeedType) >0) {
			$cedModules = $this->getCedCommerceExtensions();
			switch(trim((string)$item->update_type)) {
				case Ced_StorePickup_Model_Source_Updates_Type::TYPE_NEW_RELEASE :
				case Ced_StorePickup_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE :
					if (in_array(Ced_StorePickup_Model_Source_Updates_Type::TYPE_INSTALLED_UPDATE,$this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)],trim($item->release_version), '<')===true) {
						$isAllowed = true;
						break;
					}
				case Ced_StorePickup_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE :
					if(in_array(Ced_StorePickup_Model_Source_Updates_Type::TYPE_UPDATE_RELEASE,$this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
						$isAllowed = true;
						break;
					}
					if(in_array(Ced_StorePickup_Model_Source_Updates_Type::TYPE_NEW_RELEASE,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
				case Ced_StorePickup_Model_Source_Updates_Type::TYPE_PROMO :
					if(in_array(Ced_StorePickup_Model_Source_Updates_Type::TYPE_PROMO,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
				case Ced_StorePickup_Model_Source_Updates_Type::TYPE_INFO :
					if(in_array(Ced_StorePickup_Model_Source_Updates_Type::TYPE_INFO,$this->_allowedFeedType)) {
						$isAllowed = true;
					}
					break;
			}
		}
		return $isAllowed;
	}
	
	/**
	 * Is Country Available For Method
	 *
	 *
	 * @param $countryCode,$method
	 * @return boolean
	 */
	protected function _isCountryAvailableForMethod($countryCode, $method) {
		if (isset(self::$_internationalMethodAvailability[$countryCode])) {
			if (in_array($method, self::$_internationalMethodAvailability[$countryCode])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Is Country Available For InternationalTrackedAndSigned
	 *
	 *
	 * @param $countryCode,$method
	 * @return boolean
	 */
	public function isCountryAvailableForInternationalTrackedAndSigned($countryCode) {
		return $this->_isCountryAvailableForMethod($countryCode, self::INTERNATIONAL_TRACKED_AND_SIGNED);
	}
	
	/**
	 * Check Country Available For InternationalTracked
	 *
	 *
	 * @param $countryCode
	 * @return  boolean
	 */
	public function isCountryAvailableForInternationalTracked($countryCode) {
		return $this->_isCountryAvailableForMethod($countryCode, self::INTERNATIONAL_TRACKED);
	}
	
	/**
	 * Check Country Available For InternationalSigned
	 *
	 *
	 * @param $countryCode
	 * @return boolean
	 */
	public function isCountryAvailableForInternationalSigned($countryCode) {
		return $this->_isCountryAvailableForMethod($countryCode, self::INTERNATIONAL_SIGNED);
	}
	
	
	/**
	 * Add Insurance Charges
	 *
	 *
	 * @param $rates,$charge,$cartTotal,$valueOver
	 * @return int
	 */
	public function addInsuranceCharges($rates, $charge, $cartTotal, $valueOver = 50) {
		if($cartTotal > $valueOver) {
			for($i = 0; $i < count($rates); $i++) {
				$rates[$i]['cost'] += $charge;
			}
		}
		return $rates;
	}
	
	/**
	 * Get World Zone
	 *
	 *
	 * @param $countryCode
	 * @return null|string
	 */
	public function getWorldZone($countryCode) {
		$country = strtoupper($countryCode);
		if (in_array($country, $this->_worldZoneGb)) {
			return self::WORLD_ZONE_GB;
		} else if (in_array($country, $this->_worldZoneEu)) {
			return self::WORLD_ZONE_EU;
		} else if (in_array($country, $this->_worldZoneNonEu)) {
			return self::WORLD_ZONE_NONEU;
		} else if (in_array($country, $this->_worldZone2)) {
			return self::WORLD_ZONE_TWO;
		} else {
			return self::WORLD_ZONE_ONE;
		}
		return null;
	}
	}