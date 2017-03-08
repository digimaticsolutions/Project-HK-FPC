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
class Ced_CsOrder_Multishipping_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * Retrieve checkout state model
     *
     * @return Mage_Checkot_Model_Type_Multishipping_State
     */
    protected function _getState()
    {
        return Mage::getSingleton('checkout/type_multishipping_state');
    }


    /**
     * Create New Shipping address Form
     */
    public function newShippingAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SELECT_ADDRESSES);
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(Mage::helper('checkout')->__('Create Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/shippingSaved',array('_secure'=>true)))
                ->setErrorUrl(Mage::getUrl('*/*/*',array('_secure'=>true)));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/addresses',array('_secure'=>true)));
            }
            else {
                $addressForm->setBackUrl(Mage::getUrl('*/cart/',array('_secure'=>true)));
            }
        }
        $this->renderLayout();
    }

	/**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function shippingSavedAction()
    {
        /**
         * if we create first address we need reset emd init checkout
         */
        if (count($this->_getCheckout()->getCustomer()->getAddresses()) == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/multishipping/addresses');
    }

	/**
     * Edit shipping
     *
     */
    public function editShippingAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_SHIPPING);
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(Mage::helper('checkout')->__('Edit Shipping Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/editShippingPost', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)))
                ->setErrorUrl(Mage::getUrl('*/*/*',array('_secure'=>true)));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }

            if ($this->_getCheckout()->getCustomerDefaultShippingAddress()) {
                $addressForm->setBackUrl(Mage::getUrl('*/multishipping/shipping',array('_secure'=>true)));
            }
        }
        $this->renderLayout();
    }

	/**
     * Edit shipping post
     *
     */
    public function editShippingPostAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->updateQuoteCustomerShippingAddress($addressId);
        }
        $this->_redirect('*/multishipping/shipping');
    }

	/**
     * Select Billing
     *
     */
    public function selectBillingAction()
    {
        $this->_getState()->setActiveStep(Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING);
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

	/**
     * new billing action
     *
     */
    public function newBillingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(Mage::helper('checkout')->__('Create Billing Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/selectBilling',array('_secure'=>true)))
                ->setErrorUrl(Mage::getUrl('*/*/*',array('_secure'=>true)))
                ->setBackUrl(Mage::getUrl('*/*/selectBilling',array('_secure'=>true)));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }
	/**
     * Edit address
     *
     */
    public function editAddressAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(Mage::helper('checkout')->__('Edit Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/selectBilling',array('_secure'=>true)))
                ->setErrorUrl(Mage::getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)))
                ->setBackUrl(Mage::getUrl('*/*/selectBilling',array('_secure'=>true)));

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }

	/**
     * Edit billing
     *
     */
    public function editBillingAction()
    {
        $this->_getState()->setActiveStep(
            Mage_Checkout_Model_Type_Multishipping_State::STEP_BILLING
        );
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($addressForm = $this->getLayout()->getBlock('customer_address_edit')) {
            $addressForm->setTitle(Mage::helper('checkout')->__('Edit Billing Address'))
                ->setSuccessUrl(Mage::getUrl('*/*/saveBilling', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)))
                ->setErrorUrl(Mage::getUrl('*/*/*', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)))
                ->setBackUrl(Mage::getUrl('*/multishipping/overview',array('_secure'=>true)));
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($addressForm->getTitle() . ' - ' . $headBlock->getDefaultTitle());
            }
        }
        $this->renderLayout();
    }

	/**
     * Set Billing Action
     *
     */
    public function setBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/billing');
    }

	/**
     * Save Billing Action
     *
     */
    public function saveBillingAction()
    {
        if ($addressId = $this->getRequest()->getParam('id')) {
            Mage::getModel('checkout/type_multishipping')
                ->setQuoteCustomerBillingAddress($addressId);
        }
        $this->_redirect('*/multishipping/overview');
    }
}
