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
  * @package     Ced_CsStorePickup
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsStorePickup_Model_Vsettings_Shipping_Methods_Storepickup extends Ced_CsMultiShipping_Model_Vsettings_Shipping_Methods_Abstract
{
    protected $_code = 'ced_storepickup';
	protected $_fields = array();
	protected $_codeSeparator = '-';
	 
	/**
	 * Retreive input fields
	 *
	 * @return array
	 */
	public function getFields() {
		$fields['active'] = array('type'=>'select',
								'required'=>true,
								'values'=>array(
									array('label'=>Mage::helper('csmultishipping')->__('Yes'),'value'=>1),
									array('label'=>Mage::helper('csmultishipping')->__('No'),'value'=>0)
								)
							);

		
	  $alloptions = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
		
		if(Mage::getStoreConfig('carriers/ced_storepickup/sallowspecific')){
			$availableCountries = explode(',',Mage::getStoreConfig('carriers/ced_storeoickup/specificcountry'));
			foreach($alloptions as $key => $value){
				if(in_array($value['value'], $availableCountries)){
					$allcountry[] = $value;
				}
			}
		}else{
			$allcountry = $alloptions;
		}
	
		
		$fields['allowed_country'] = array('type'=>'multiselect',
									'values'=>$allcountry
									);		
		return $fields;
	}
	
	/**
	 * Retreive labels
	 *
	 * @param string $key
	 * @return string
	 */
	public function getLabel($key) {
		switch($key) {
			case 'label' : return Mage::helper('usa')->__('Store Pickup');break;
			case 'allowed_country': return Mage::helper('usa')->__('Allowed Country');break;
			case 'active': return Mage::helper('usa')->__('Active');break;
			default : return parent::getLabel($key); break;
		}
	}
	
}
