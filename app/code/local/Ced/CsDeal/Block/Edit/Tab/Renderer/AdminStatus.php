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
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Ced_CsDeal_Block_Edit_Tab_Renderer_AdminStatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$vOptionArray=Mage::getModel('Ced_CsDeal_Model_Deal')->getMassActionArray();
		switch ($row->getAdminStatus()) {
			case Ced_CsDeal_Model_Deal::STATUS_APPROVED;
				return $vOptionArray[Ced_CsDeal_Model_Deal::STATUS_APPROVED];
				break;
			case Ced_CsDeal_Model_Deal::STATUS_NOT_APPROVED;
				return $vOptionArray[Ced_CsDeal_Model_Deal::STATUS_NOT_APPROVED];
				break;	
			
			default:
				return $vOptionArray[Ced_CsDeal_Model_Deal::STATUS_PENDING];
				break;
		}
	}

}
?>
