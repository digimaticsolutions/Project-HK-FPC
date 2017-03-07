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

class Ced_CsCmsPage_Block_Adminhtml_Cmspage_Entity_Grid_Renderer_Approve extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(Varien_Object $row) {
		$html = '';
		if($row->getPageId()!='' && $row->getIsApprove() == '0') {
			$url =  $this->getUrl('*/*/approved', array('page_id' => $row->getPageId()));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.Mage::helper('cscmspage')->__('Approve').'</a>';  
		} 
				
		if($row->getPageId()!='' && $row->getIsApprove() == '1') {
			if(strlen($html) > 0) $html .= ' | ';
			$url =  $this->getUrl('*/*/disapproved', array('page_id' => $row->getPageId()));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.$this->__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.Mage::helper('cscmspage')->__('Disapprove')."</a>";
		}
		
		return $html;
	}
}