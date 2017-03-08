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
 * @category    Ced;
 * @package     Ced_CsOrder 
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com> 
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsOrder_Model_Design_Package extends Mage_Core_Model_Design_Package
{
	/**
	 * @var Mage_Core_Model_Design_Fallback
	 */
	protected $_fallback = null;
	
	/**
     * Use this one to get existing file name with fallback to default
     *
     * $params['_type'] is required
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
        Varien_Profiler::start(__METHOD__);        
        $this->updateParamDefaults($params);
        
    	if(	((strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/shipment/new") !== false) 
		|| (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/shipment/view") !== false)  
		|| (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/shipment/addTrack") !== false) 
		|| (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/shipment/addComment") !== false)
		|| (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/invoice/new") !== false)
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/invoice/view") !== false) 
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/invoice/addComment") !== false)
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/invoice/updateQty") !== false)

        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/vorders") !== false) 
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/creditmemo/view") !== false) 
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/creditmemo/new") !== false) 
        || (strpos(Mage::helper('core/url')->getCurrentUrl(),"csorder/creditmemo/updateQty") !== false) 
		)
    		&& $params['_area']=="adminhtml" 
    		&& ($params['_package']!=="default" || $params['_theme']!=="default")
    	){ 
    		$params['_package']='default';
    		$params['_theme']='default';
    	}
   		if(version_compare(Mage::getVersion(), '1.8.1.0', '<=')) {
    		$result = $this->_fallback($file, $params, array(
    				array(),
    				array('_theme' => $this->getFallbackTheme()),
    				array('_theme' => self::DEFAULT_THEME),
    		));
    	}
        else{
        	$result = $this->_fallback(
        			$file,
        			$params,
        			$this->_fallback->getFallbackScheme(
        					$params['_area'],
        					$params['_package'],
        					$params['_theme']
        			)
        	);
        }
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }
}
