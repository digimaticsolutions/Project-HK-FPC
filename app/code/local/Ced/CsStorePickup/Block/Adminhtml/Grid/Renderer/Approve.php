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

class Ced_CsStorePickup_Block_Adminhtml_Grid_Renderer_Approve extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(Varien_Object $row) {
		$html = '';
		
		if($row->getPickupId()!='' && $row->getIsApproved() == '0') {
			$url =  $this->getUrl('*/*/approved', array('pickup_id' => $row->getPickupId()));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.Mage::helper('catalog')->__('Approve').'</a>';  
		} 
				
		if($row->getPickupId()!='' && $row->getIsApproved() == '1') {
			if(strlen($html) > 0) $html .= ' | ';
			$url =  $this->getUrl('*/*/disapproved', array('pickup_id' => $row->getPickupId()));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.Mage::helper('catalog')->__('Disapprove')."</a>";
		}
		
		return $html;
	}
}