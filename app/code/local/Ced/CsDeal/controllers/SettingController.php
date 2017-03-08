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
/**
 * Deal controller
 *
 * @category   Ced
 * @package    Ced_CsDeal
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 *
 */
class Ced_CsDeal_SettingController extends  Ced_CsMarketplace_Controller_AbstractController {

	public function indexAction(){

		$this->loadLayout();
		$this->_title($this->__("Deal"));
		$this->_title($this->__("Deal Setting "));
		$this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
		//var_dump(Mage::getSingleton('core/layout')->getUpdate()->getHandles());
	}
		public function saveAction(){
		if($this->getRequest()->isPost()){
			$post_data=$this->getRequest()->getPost();
			$post_data['store']=Mage::app()->getStore()->getStoreId();
			$setting_id=$post_data['setting_id'];
			unset($post_data['setting_id']);
			if($setting_id){
				$model=Mage::getModel('csdeal/dealsetting')->load($setting_id);
				$model->addData($post_data);
			}else{
				$model=Mage::getModel('csdeal/dealsetting');
				$model->addData($post_data);
			}
			$model->save();
			Mage::getSingleton('customer/session')->addSuccess('Setting saved Successfully');
			$this->_redirect('*/*/');
		}else{
			$this->_redirect('*/*/');
		}

	}
	
}	
