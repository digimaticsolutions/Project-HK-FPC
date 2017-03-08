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
  * @package     Ced_CsCmsPage
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

class Ced_CsCmsPage_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
 	/**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    { 
        /* @var $front Mage_Core_Controller_Varien_Front */
    	
        $front = $observer->getEvent()->getFront();

        $front->addRouter('cscmspage', $this);
	
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
	public function match(Zend_Controller_Request_Http $request)
    {   
    	
    	if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('cscmspage_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();
		
        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }

        $page   = Mage::getModel('cscmspage/cmspage');
       
        $pageId = $page->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (!$pageId) {
        	return false;
        }
        
       try{
       	$request->setModuleName('cscmspage')
       	->setControllerName('page')
       	->setActionName('view')
       	->setParam('page_id', $pageId);
       	$request->setAlias(
       			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
       			$identifier
       	);
       }
		catch(Exception $e)
		{
			die($e);
		}

        return true;
    }
}
