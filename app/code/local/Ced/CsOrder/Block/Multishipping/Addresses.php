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
class Ced_CsOrder_Block_Multishipping_Addresses extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return Mage::getSingleton('csorder/type_multishipping');
    }

	/**
	 * Prepare Layout
	 *
	 * @return object
	 */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Ship to Multiple Addresses') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

	/**
	 * Get Items
	 *
	 * @return object
	 */
    public function getItems()
    {
		
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param  $item
     * @return string
     */
	public function getAddressesHtmlSelect($item, $index)
    {
	  $vendorId = Mage::getModel('csmarketplace/vproducts')->getVendorIdByProduct($item->getProduct()->getId());
	  $storeId = Mage::app()->getStore()->getId();
	   if($vendorId && Mage::getConfig()->getModuleConfig('Ced_CsMultiShipping')->is('active', 'true') && Mage::getStoreConfig('ced_csmultishipping/general/activation', $storeId)) {
		$helper = Mage::helper('csmultishipping');
		$activeMethods = $helper->getActiveVendorMethods($vendorId);
		   $vendorAddress = $helper->getVendorAddress($vendorId);
		if(count($activeMethods) < 1 || !$helper->validateAddress($vendorAddress)
		|| !$helper->validateSpecificMethods($activeMethods)){
		 $vendorId = 0;
		}
	   }
   
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setId('ship_'.$index.'_'.$item->getQuoteItemId().'_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions($vendorId));

        return $select->getHtml();
    }
    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions($vendorId)
    {
        $options = $this->getData('address_options');
        if (is_null($options)||true) {
            $options = array();
			if($vendorId){
				$addresses=Mage::getResourceModel('customer/address_collection')->setVendorId($vendorId)->setForVendor(1)->setCustomerFilter($this->getCustomer())
                ->addAttributeToSelect('*');
			}
			else
			{
				$addresses=$this->getCustomer()->getAddresses();
			}
            foreach ($addresses as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
		
            $this->setData('address_options', $options);
        }

        return $options;
    }

	/**
	 * get current customer
	 *
	 * @return object
	 */
    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

	/**
	 * Get item url
	 *
	 * @return string
	 */
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/'.$item->getProductId());
    }
	
    public function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', array('address'=>$item->getQuoteAddressId(), 'id'=>$item->getId(),'_secure'=>true));
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost',array('_secure'=>true));
    }

    public function getNewAddressUrl()
    {
        return Mage::getUrl('*/multishipping_address/newShipping',array('_secure'=>true));
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/cart/',array('_secure'=>true));
    }

    public function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
