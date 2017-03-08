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
 * Ced_CsCommission source updates type
 *
 * @category    Ced
 * @package     Ced_CsCommission
 * @author 		CedCommerce Core Team <connect@cedcommerce.com >
 */ 
 
class Ced_CsCommission_Model_Source_Vendor_Rate_Aggregrade extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const TYPE_MAX	= 'MAX';
	const TYPE_MIN	= 'MIN';
	
	/**
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
	    $hlp = Mage::helper('cscommission');
		return array(
			array('value' => self::TYPE_MAX, 'label' => $hlp->__('max()')),
			array('value' => self::TYPE_MIN, 'label' => $hlp->__('min()'))
		);
	}
	
	/**
     * Retrive all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
    	return $this->toOptionArray();
	}
	
	
	/**
	 * Returns label for value
	 * @param string $value
	 * @return string
	 */
	public function getLabel($value)
	{
		$options = $this->toOptionArray();
		foreach($options as $v){
			if($v['value'] == $value){
				return $v['label'];
			}
		}
		return '';
	}
	
	/**
	 * Returns array ready for use by grid
	 * @return array 
	 */
	public function getGridOptions()
	{
		$items = $this->getAllOptions();
		$out = array();
		foreach($items as $item){
			$out[$item['value']] = $item['label'];
		}
		return $out;
	}
}
