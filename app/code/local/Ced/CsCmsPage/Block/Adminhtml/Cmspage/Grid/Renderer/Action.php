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

class Ced_CsCmsPage_Block_Adminhtml_Cmspage_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * 
	 * @param Varien_Object $row
	 * @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract::render()
	 * @return html
	 */
    public function render(Varien_Object $row)
    {
    	$urlModel = Mage::getModel('core/url')->setStore($row->getData('_first_store_id'));
        $href = $urlModel->getUrl(
            $row->getIdentifier(), array(
                '_current' => false,
                '_query' => '___store='.$row->getStoreCode().'&vid='.$row->getVendorId()
           )
        );
        
        return '<a href="'.$href.'" target="_blank">'.$this->__('Preview').'</a>';
    }
}
