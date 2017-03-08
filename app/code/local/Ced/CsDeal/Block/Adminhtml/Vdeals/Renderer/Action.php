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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */ 
   
class Ced_CsDeal_Block_Adminhtml_Vdeals_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
        $sure="you Sure?";
        if($row->getAdminStatus()==Ced_CsDeal_Model_Deal::STATUS_APPROVED)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vdeals/changeStatus/status/0/deal_id/' . $row->getId()).'" title="'.$this->__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.$this->__("Disapprove").'</a>';
        if($row->getAdminStatus()==Ced_CsDeal_Model_Deal::STATUS_PENDING)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vdeals/changeStatus/status/1/deal_id/' . $row->getId()).'"  title="'.$this->__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.$this->__("Approve").'</a>
        		 | <a href="'.$this->getUrl('adminhtml/adminhtml_vdeals/changeStatus/status/0/deal_id/' . $row->getId()).'" title="'.$this->__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.$this->__("Disapprove").'</a>';
        if($row->getAdminStatus()==Ced_CsDeal_Model_Deal::STATUS_NOT_APPROVED)
        	$html='<a href="'.$this->getUrl('adminhtml/adminhtml_vdeals/changeStatus/status/1/deal_id/' . $row->getId()).'"  title="'.$this->__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.$this->__("Approve").'</a>';
        return $html;
    }

}
?>