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
class Ced_CsOrder_Model_Resource_Address_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
	public $for_vendor;
	public $vendor_id;
    /**
     * Resource initialization
     */
    protected function _construct()
    {
		
        $this->_init('customer/address');
    }

    /**
     * Set customer filter
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Resource_Address_Collection
     */
    public function setCustomerFilter($customer)
    {
        if ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
        } else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
		if($this->for_vendor)
		{
			
			if($this->for_vendor==1)
			{
				$existingAddresses=Mage::getResourceModel('customer/address_collection')->setVendorId($this->vendor_id)->setForVendor(3)->setCustomerFilter($customer)
                ->addAttributeToSelect('*');
				$existingArray=array();
				foreach($existingAddresses as $address)
				{
					$existingArray[$address->getVendor_reference()]=$address->getId();
				}
				
				foreach($customer->getAddresses() as $address)
				{
					
					if(!isset($existingArray[$address->getId().'_'.$this->vendor_id]))
					{
						$data=$address->getData();
						unset($data['entity_id']);
						$customAddress = Mage::getModel('customer/address');
						
						$customAddress->setData($data)
						->setCustomerId($customer->getId())
						->setSaveInAddressBook('1')
						->setVendorId($this->vendor_id)
						->setForVendor(1)
						->setVendorReference($address->getId().'_'.$this->vendor_id)
						->save();
						
					}
					 else
					{

					
						$data=$address->getData();
					
						$existingAddress=Mage::getResourceModel('customer/address_collection')->setForVendor(2)->setCustomerFilter($customer)->addAttributeToFilter('vendor_reference',$address->getId().'_'.$this->vendor_id)->getFirstItem();
						
						if($existingAddress->getId()){
							$existingAddress
							->setFirstname($data['firstname'])
							->setLastname($data['lastname'])
							->setCompany($data['company'])
							->setStreet($data['street'])
							->setCity($data['city'])
							->setCountryId($data['country_id'])
							->setPostcode($data['postcode'])
							->setTelephone($data['telephone'])
							->save();
						}
						
					
					} 
						
				}
				return Mage::getResourceModel('customer/address_collection')->setVendorId($this->vendor_id)->setForVendor(3)->setCustomerFilter($customer)
                ->addAttributeToSelect('*');
			}
			elseif($this->for_vendor==2)
			{
				$this->addAttributeToFilter('for_vendor',1); 
			}
			else
			{
				 $this->addAttributeToFilter('vendor_id',$this->vendor_id);
				$this->addAttributeToFilter('for_vendor',1); 
			}
		}
		else
		{
			$this->addAttributeToFilter('for_vendor',array(
			array(
				'neq' =>1
			)));  
		}
        return $this;
    }
	

	public function setForVendor($val)
	{
		$this->for_vendor=$val;
		return $this;
	}
	public function setVendorId($val)
	{
		$this->vendor_id=$val;
		return $this;
	}
}
