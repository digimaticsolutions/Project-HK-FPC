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
 * @package     Ced_CsSubAccount
 * @author   CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer register form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ced_CsSubAccount_Block_Form_Register extends Mage_Directory_Block_Data
{
    /**
     * Address instance with data
     *
     * @var Mage_Customer_Model_Address
     */
    protected $_address;

    protected function _prepareLayout()
    { return parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Create New Customer Account'));
        
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('cssubaccount/customer/createPost');
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->helper('customer')->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('customer/session')->getCustomerFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return int
     */
    public function getRegion()
    {
        if (false !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } else if (false !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }

    /**
     *  Newsletter module availability
     *
     *  @return boolean
     */
    public function isNewsletterEnabled()
    {
        return Mage::helper('core')->isModuleOutputEnabled('Mage_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = Mage::getModel('customer/address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param Mage_Customer_Model_Form $form
     * @return Mage_Customer_Block_Form_Register
     */
    public function restoreSessionData(Mage_Customer_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }
}
