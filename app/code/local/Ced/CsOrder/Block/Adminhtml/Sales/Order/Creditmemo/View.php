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
class Ced_CsOrder_Block_Adminhtml_Sales_Order_Creditmemo_View extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
{

	/**
     * Add & remove control buttons
     *
     */
    public function __construct()
    {
        parent::__construct();
		$this->setTemplate('csorder/widget/form/container.phtml');
		$this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back btn btn-warning',
        ), -1);
		
		 if ($this->getCreditmemo()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('sales')->__('Print'),
                'class'     => 'btn btn-danger uptransform',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
        }
		
    }
	/**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
       
	   	$vorder = Mage::getModel("csmarketplace/vorders")->getVorderByCreditmemo($this->getCreditmemo());

	    return $this->getUrl(
            'csorder/vorders/view',
            array(
                'order_id'  => $vorder->getId(),
                'active_tab'=> 'order_creditmemos','_secure'=>true
            ));
    }
	/**
     * Retrieve print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'creditmemo_id' => $this->getCreditmemo()->getId(),'_secure'=>true
        ));
    }
	
	
	/**
     * Update 'back' button url
     *
     * @return Mage_Adminhtml_Block_Widget_Container | Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag == "creditmemo") {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getUrl("*/creditmemo/") . '\')'
                );

        }
        return $this;
    }
	
	/**
     * Enter description here...
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'core/url';
    }

}
