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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Productfaq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_Action {
	
	/**
	 * Faqs grid page
	 */
	public function indexAction() {
		$enable = Mage::getStoreConfig ( 'productfaq/general/enable' );
		$this->loadLayout ()->_setActiveMenu ( 'ced_productfaq' )->_title ( $this->__ ( 'FAQ MANAGEMENT' ) );
		$this->renderLayout ();
	}
	/**
	 * Get Faq Model
	 */
	protected function _getFaq() {
		return Mage::getModel ( 'productfaq/productfaq' );
	}
	/**
	 * New FAQ action
	 */
	public function newAction() {
		$this->loadLayout ();
		$this->_addContent ( $this->getLayout ()->createBlock ( 'productfaq/adminhtml_faq_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'productfaq/adminhtml_faq_edit_tabs' ) );
		$this->renderLayout ();
	}
	/**
	 * Edit Faq
	 */
	public function editAction() {
		$id = ( int ) $this->getRequest ()->getParam ( 'id' );
		$model = $this->_getFaq ();
		if ($id) {
			$model->load ( $id );
			if ($model->getId ()) {
				$data = Mage::getSingleton ( 'productfaq/productfaq' )->getFormData ( true );
				if ($data) {
					$model->setData ( $data )->setProductId ( $id );
				}
			} else {
				$this->_getSession ()->addError ( Mage::helper ( 'productfaq' )->__ ( 'Example does not exist' ) );
				$this->_redirect ( '*/*/index' );
			}
		}
		Mage::register ( 'productfaq_data', $model );
		$this->_forward ( 'new' );
	}
	/**
	 * Save Faq from edit faq page
	 */
	public function saveAction() {
		$id = ( int ) $this->getRequest ()->getParam ( 'id' );
		$productid = $this->getRequest ()->getParam ( 'product_id' );
		$title = $this->getRequest ()->getParam ( 'title' );
		$description = $this->getRequest ()->getParam ( 'description' );
		$email = $this->getRequest ()->getParam ( 'email_id' );

		$visibility = $this->getRequest ()->getParam ( 'visible_on_frontend' );
		$question = $this->_getFaq ();
		$question->setData ( 'product_id', $productid )->setData ( 'title', $title )->setData ( 'description', $description )->setData ( 'visible_on_frontend', $visibility );
		$question->setId ( $id );
		$question->save ();
		$this->_getSession ()->addSuccess ( $this->__ ( 'The Data has been saved.' ) );
		if (! empty ( $email )) {
			$this->sendTransactional ( $email, $description );
		}
		if ($this->getRequest ()->getParam ( 'back' )) {
			$this->_redirect ( '*/*/edit', array (
					'id' => $question->getId () 
			) );
			return;
		}
		$this->_redirect ( "*/*/" );
	}
	/**
	 * Send Notification Mail to customer
	 */
	public function sendTransactional($email, $description) {
		$storeId = Mage::app ()->getStore ()->getStoreId ();
		/* Code to lode custom email template */
		$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'productfaq_faqs_email_product_faq_template' );
		$from_name = Mage::getStoreConfig ( 'productfaq/general/sendername' );
		$senderEmail = Mage::getStoreConfig ( 'productfaq/general/senderemail' );
		$emailTemplateVariables = array();
		$emailTemplateVariables['answer'] = $description;
		$emailTemplate->setTemplateSubject('ProductFaq Email');
		$emailTemplate->setSenderName($from_name);
		$emailTemplate->setSenderEmail($senderEmail);
		try
		{
			$emailTemplate->send($email, 'test', $emailTemplateVariables);
		}
		catch(Exception $e)
		{
			print_r($e->getMessage());die('--here');
				$this->_getSession ()->addError($e->getMessage());
		}
















		// Create an array of variables to assign to template
		//print_r($emailTemplate->getData());die('---check');
		/*$emailTemplateVariables = array (
				'answer' => $description 
		);
		$processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
		$to_email = $email;
		$to_name = 'test';
		$subject = 'ProductFaq Email';
		$Body = $processedTemplate;
		$senderEmail = Mage::getStoreConfig ( 'productfaq/general/senderemail' );
		$senderName = Mage::getStoreConfig ( 'productfaq/general/sendername' );
		$mail = new Zend_Mail (); // class for mail
		$mail->setBodyHtml ( $Body ); // for sending message containing html code
		$mail->setFrom ( $senderEmail, $senderName );
		$mail->addTo ( $to_email, $to_name );
		//print_r($mail);die('--here');
		// $mail->addCc($cc, $ccname); //can set cc
		// $mail->addBCc($bcc, $bccname); //can set bcc
		$mail->setSubject ( $subject );
		try{
				$mail->send ();
		}catch (Exception $e) {
			print_r($e);die('--here');
				$this->_getSession ()->addError($e->getMessage());
			}*/
		
		/* Code to lode custom email template */
		
		return;
	}
	/**
	 * FAQ delete action
	 */
	public function deleteAction() {
		if ($id = $this->getRequest ()->getParam ( 'id' )) {
			try {
				$model = $this->_getFaq ();
				$model->setId ( $id );
				$model->delete ();
				Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'The example has been deleted.' ) );
				$this->_redirect ( '*/*/' );
				return;
			} catch ( Exception $e ) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
				$this->_redirect ( '*/*/edit', array (
						'id' => $this->getRequest ()->getParam ( 'id' ) 
				) );
				return;
			}
		}
		Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'productfaq' )->__ ( 'Unable to find the example to delete.' ) );
		$this->_redirect ( '*/*/' );
	}
	/**
	 * FAQ's mass visibility action
	 */
	public function massVisibiltyAction() {
		if ($this->getRequest ()->isPost ()) {
			
			// ass action
			$visibility = $this->getRequest ()->getPost ( 'visibility' );
			if ($visibility == 'visible') {
				$visibility = 1;
			} else {
				$visibility = 0;
			}
			$questionids = $this->getRequest ()->getPost ( 'faq_ids', array () );
			foreach ( $questionids as $id ) {
				$question = Mage::getModel ( 'productfaq/productfaq' )->load ( $id );
				if ($question->getId ()) {
					try {
						$question->setData ( 'visible_on_frontend', $visibility );
						$question->save ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'Status successfully updated' ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'productfaq' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_redirectReferer ();
	}
	/**
	 * FAQQ's mass delete action
	 */
	public function massDeleteAction() {
		if ($this->getRequest ()->isPost ()) { // ass action
			$trnIds = $this->getRequest ()->getPost ( 'faq_ids', array () );
			foreach ( $trnIds as $id ) {
				
				$trn = $this->_getFaq ()->load ( $id );
				if ($trn->getId ()) {
					try {
						$trn->delete ();
						$this->_getSession ()->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'FAQ %s successfully deleted.', $id ) );
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'productfaq' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		} else {
			$id = $this->getRequest ()->getParam ( 'id' );
			try {
				$trn = $this->_getFaq ()->load ( $id );
				$trn->setId ( $id )->delete ();
				$this->_getSession ()->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'Transaction %s successfully deleted.', $id ) );
			} catch ( Exception $e ) {
				$this->_getSession ()->addError ( $e->getMessage () );
			}
		}
		$this->_redirectReferer ();
	}
}