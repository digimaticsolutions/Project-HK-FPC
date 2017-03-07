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
 * @package     Ced_CsProductfaq
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ced_CsProductfaq_csfaqController extends Ced_CsMarketplace_Controller_AbstractController {
	
 /**
     * Default vendor products list page
     */
    public function indexAction() {
        if(!$this->_getSession()->getVendorId()) return;
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );       
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper('csmarketplace')->__ ('Faq List') );
        
        $this->renderLayout ();
    }


	/**
	 * Vendor CMS grid for AJAX request
	 */
	public function gridAction()
	{
		if(!$this->_getSession()->getVendorId())
			return;
		
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function newAction() {
		$this->loadLayout ();
		//$this->_addContent ( $this->getLayout ()->createBlock ( 'productfaq/adminhtml_faq_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'productfaq/adminhtml_faq_edit_tabs' ) );
		$this->renderLayout ();
		$this->_forward('edit');
	}

	public function editAction() {
		
		$this->loadLayout ();
		$this->renderLayout ();
		
	}
  public function saveAction() {
        
         if(!$this->_getSession()->getVendorId())
            return; 
        
        $vid = $this->_getSession()->getVendorId();
        if($vid == 0)
        {
            return;
        }
        
        $vendor = Mage::getModel('csmarketplace/vendor')->load($vid);
        $Vname = $vendor->getName();
        $Vemail = $vendor->getEmail();
        $data = $this->getRequest()->getPost();
       
        if(!isset($data['vfaq']))
        {
        	return $this->_redirect('*/*/index');
        }
     
        $description = $data['vfaq']['answer'];
        $email = $data['vfaq']['Email'];
       
    
        $model = Mage::getModel ('productfaq/productfaq');
        if ($data) {
            $id = $this->getRequest()->getParam('id');
           
           if ($id) {
                $model->load($id);
                $model->setData('product_id',$data['vfaq']['productId']);
                $model->setData('title',$data['vfaq']['title']);
                $model->setData('description',$data['vfaq']['answer']);
                $model->setData('email_id',$data['vfaq']['Email']);
                $model->setData('visible_on_frontend',$data['vfaq']['status']);
                
               

                try {
                    $model->save();
                    if($data['vfaq']['Email']) {
                        $this->sendTransactional($email, $description,$Vname,$Vemail);
                        
                    }
                    Mage::getSingleton ( 'adminhtml/session')->addError(Mage::helper('productfaq')->__('The Faq has been saved.'));
                
                    if ($this->getRequest()->getParam('back')) {
                        return $this->_redirect ('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    }
                   $this->_redirect('*/*/index');
                } catch (Exception $e) {
                    Mage::getSingleton ( 'adminhtml/session')->addError(Mage::helper('productfaq')->__('Something went wrong while saving the faq.'));
                }

                
                return $this->_redirect('*/*/index');
            }
            else{
            
                $pids = $data['vfaq']['products'] ;
                
                foreach ($pids as $k=>$v)
                {
                    $model = Mage::getModel ('productfaq/productfaq');
                    $model->setData('product_id',$v);
                    $model->setData('title',$data['vfaq']['title']);
                    $model->setData('description',$data['vfaq']['answer']);
                    $model->setData('email_id',$data['vfaq']['Email']);
                    $model->setData('visible_on_frontend',$data['vfaq']['status']);
              
                    $model->setData('vendor_id',$vid);
                    
                    try {
                        $model->save();
                    } catch (Exception $e) {
                    	Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productfaq')->__('Something went wrong while saving the faq.'));
                    	
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productfaq')->__('The faq has been successfully saved'));
               
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $model->getId(),'_current' => true]);
                }
                return $this->_redirect ('*/*/');
            }
        }
        return $this->_redirect('*/*/');
    }
	/**
	 * Send Notification Mail to customer
	 */
	public function sendTransactional($email, $description,$Vname,$Vemail) {
		
		
		//print_r($email); echo "<br>";print_r($description); echo "<br>";print_r($Vname); echo "<br>";print_r($Vemail); die("kljh");
	
		$storeId = Mage::app ()->getStore ()->getStoreId ();
		/* Code to lode custom email template */
		$emailTemplate = Mage::getModel ('core/email_template')->loadDefault('productfaq_faqs_email_product_faq_template');
		
		// Create an array of variables to assign to template
		$emailTemplateVariables = array (
				'answer' => $description 
		);
		$processedTemplate = $emailTemplate->getProcessedTemplate ($emailTemplateVariables);
		$to_email = $email;
		$to_name = 'test';
		$subject = 'Vendor ProductFaq Email';
		$Body = $processedTemplate;
		$senderEmail = $Vemail;
		$senderName = $Vname;
		$mail = new Zend_Mail (); 
	//print_r($Body);die("kj");
		$mail->setBodyHtml($Body); // for sending message containing html code
		$mail->setFrom($senderEmail, $senderName);
		$mail->addTo($to_email,$to_name);
		// $mail->addCc($cc, $ccname); //can set cc
		// $mail->addBCc($bcc, $bccname); //can set bcc
		$mail->setSubject($subject);
		$mail->send();
		
		/* Code to lode custom email template */
		
		return;
	}
	/**
	 * FAQ delete action
	 */
	public function deleteAction() {
		if ($id = $this->getRequest ()->getParam ( 'id' )) {
			
			
			try {
				$model = Mage::getModel('productfaq/productfaq');
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
	public function massEnableAction() {
		if ($this->getRequest()->getPost ()) {
				
			
			$ids = $this->getRequest()->getPost ('id');
			$ids = explode(',', $ids);
			
			foreach ($ids as $id) {
				$question = Mage::getModel ( 'productfaq/productfaq' )->load ( $id );
				if ($question->getId ()) {
					try {
						$question->setData ( 'visible_on_frontend', 1 );
						$question->save ();
						
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'productfaq' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_getSession ()->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'Status successfully updated' ) );
		$this->_redirectReferer ();
	}
	
	public function massDisableAction() {
		if ($this->getRequest()->getPost ()) {
				
			
			$ids = $this->getRequest()->getPost ('id');
			$ids = explode(',', $ids);
			
			foreach ($ids as $id) {
				$question = Mage::getModel ( 'productfaq/productfaq' )->load ( $id );
				if ($question->getId ()) {
					try {
						$question->setData ( 'visible_on_frontend', 0 );
						$question->save ();
						
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession()->addError( Mage::helper ('productfaq')->__('Invalid id supplied, %s', $id));
				}
			}
		}
		$this->_getSession()->addSuccess(Mage::helper('productfaq')->__ ('Status successfully updated'));
		$this->_redirectReferer();
	}
	
	
	
	/**
	 * FAQQ's mass delete action
	 */
	public function massDeleteAction() {
		if ($this->getRequest()->getPost ()) {
				
			
			$ids = $this->getRequest()->getPost ('id');
			$ids = explode(',', $ids);
			
			foreach ($ids as $id) {
				$question = Mage::getModel ( 'productfaq/productfaq' )->load ( $id );
				if ($question->getId ()) {
					try {
						$question->delete();
						
					} catch ( Exception $e ) {
						$this->_getSession ()->addError ( $e->getMessage () );
					}
				} else {
					$this->_getSession ()->addError ( Mage::helper ( 'productfaq' )->__ ( 'Invalid id supplied, %s', $id ) );
				}
			}
		}
		$this->_getSession ()->addSuccess ( Mage::helper ( 'productfaq' )->__ ( 'The question successfully deleted. ' ) );
		$this->_redirectReferer ();
	}




}
