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
 
class Ced_CsDeal_Block_Grid_Renderer_Deal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {
		$url=Mage::getUrl('csdeal/deal/dealpopup',array('id'=>$row->getId(),'name'=>$row->getName()));

		return '<div id="dealbuttion">
				<button class="btn btn-warning" title="Create Deal" onclick="createDeal('.$row->getId().')" >Create Deal</button>
				<input id="create_url'.$row->getId().'" type="hidden" value="'.$url.'" />
				</div>
				<script>
				function createDeal(id){
					var element_id="create_url"+id;
					var url= document.getElementById(element_id).value;
					
					document.location.href=url;
				}

				</script>

				';
	}

}
?>
