<?php

require_once "Mage/Checkout/controllers/OnepageController.php";
class Ced_CsMarketplace_Checkout_OnepageController extends Mage_Checkout_OnepageController
{

public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
			$contact = $data['telephone'];
			$customer = Mage::getResourceModel('customer/address_collection')
			->addAttributeToSelect('telephone')
			->addAttributeToFilter('telephone', $contact);
			//print_r(count($customer));
			if(!empty($customer->getData()))
			{
				$result['error'] = $this->__('This Phone number already exists for another customer.');
				$result['error_messages'] = $this->__('This Phone number already exists for another customer.');
				$result['success'] = false;
				$result['error'] = true;
				$result['message'] = $this->__('This Phone number already exists for another customer.');
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				return;
			}
			else 
			{
			 if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }
			}
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
	

}