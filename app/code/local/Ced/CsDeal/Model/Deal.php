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
 * @package     Ced_OrderDelete
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 **/
class Ced_CsDeal_Model_Deal extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("csdeal/deal");
    }
    const STATUS_APPROVED          = '1';
    const STATUS_NOT_APPROVED      = '0';
    const STATUS_PENDING           = '2';

    protected function _getSession() {
		return Mage::getSingleton('customer/session');
	}

   	public function getMassActionArray() {
		return array (
				'-1'  => Mage::helper('csdeal')->__(''),
				self::STATUS_APPROVED  => Mage::helper('csdeal')->__('Approved'),
				self::STATUS_NOT_APPROVED => Mage::helper('csdeal')->__('Disapproved') ,
				self::STATUS_PENDING => Mage::helper('csdeal')->__('Approval Pending') 
		);
	}
	public function getVendorDealProductIds($id) {
		return $this->getResource()->getVendorDealProductIds($id);
	}
	

	/**
	 * Validate csmarketplace product attribute values.
	 * @return array $errors
	 */
	public function validate(){
		$errors = array();
		$helper=Mage::helper('csdeal');
		if (!Zend_Validate::is( trim($this->getName()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product Name cannot be empty');
		}
		if (!Zend_Validate::is( trim($this->getSku()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product SKU cannot be empty');
		}
		
		if($this->getType()==Mage_Catalog_Model_Product_Type::TYPE_SIMPLE){
			$weight=	trim($this->getWeight());
			if (!Zend_Validate::is($weight, 'NotEmpty')) {
				$errors[] = $helper->__('The Product Weight cannot be empty');
			}
			else if(!is_numeric($weight)&&!($weight>0))
				$errors[] = $helper->__('The Product Weight must be 0 or Greater');
		}
		
		$qty=trim($this->getQty());
		if (!Zend_Validate::is( $qty, 'NotEmpty')) {
			$errors[] = $helper->__('The Product Stock cannot be empty');
		}
		else if(!is_numeric($qty))
			$errors[] = $helper->__('The Product Stock must be a valid Number');
			
		if (!Zend_Validate::is( trim($this->getTaxClassId()) , 'NotEmpty')) {
			$errors[] = $helper->__('The Product Tax Class cannot be empty');
		}
		
		$price=trim($this->getPrice());
		if (!Zend_Validate::is( $price, 'NotEmpty')) {
			$errors[] = $helper->__('The Product Price cannot be empty');
		}
		else if(!is_numeric($price)&&!($price>0))
			$errors[] = $helper->__('The Product Price must be 0 or Greater');
		
		$special_price=trim($this->getSpecialPrice());
		if($special_price!=''){
			if(!is_numeric($special_price)&&!($special_price>0))
			$errors[] = $helper->__('The Product Special Price must be 0 or Greater');
		}
		
		$shortDescription=strip_tags(trim($this->getShortDescription()));
		$description=strip_tags(trim($this->getDescription()));	
		if (strlen($shortDescription)==0) {
			$errors[] = $helper->__('The Product Short description cannot be empty');
		}
		if (strlen($description)==0) {
			$errors[] = $helper->__('The Product Description cannot be empty');
		}
		if (empty($errors)) {
			return true;
		}
		return $errors;
	}
	/**
	 * Change Vproduct status
	 * @params int $productId,int checkstatus
	 * 
	 */
	public function changeVdealStatus($dealIds,$checkstatus){
		$errors=array();
		if(is_array($dealIds)){
			if(count($dealIds)>0){
				foreach ($dealIds as $dealId){
					if($dealId){
						$row=Mage::getModel('csdeal/deal')->load($dealId);
						if($row->getAdminStatus()!=$checkstatus){
							switch ($checkstatus){
								case Ced_CsDeal_Model_Deal::STATUS_APPROVED:
									$row->setAdminStatus(Ced_CsDeal_Model_Deal::STATUS_APPROVED);				
									$errors['success']=1;
									break;
									
								case Ced_CsDeal_Model_Deal::STATUS_NOT_APPROVED:
									$row->setAdminStatus(Ced_CsDeal_Model_Deal::STATUS_NOT_APPROVED);
									$errors['success']=1;
									break;
							}
							$row->save();
						}
						else 
							$errors['success']=1;
					}else{
						$errors['error']=1;
					}	
				}	
			}
		}else{
			$row=Mage::getModel('csdeal/deal')->load($dealIds);
			if($row->getDealId()){
				$row->setAdminStatus($checkstatus);
				$row->save();
				$errors['success']=1;
			}else{
				$errors['error']=1;
			}			
		}
		return $errors;
	}
	public function getDealProductIds()
	{
		return $this->getResource()->getDealProductIds();
	}
}
	 
