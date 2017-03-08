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
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_CsSubAccount_Model_Acl extends Zend_Acl
{
    /**
     * All the group roles are prepended by G
     *
     */
    const ROLE_TYPE_GROUP = 'G';
    
    /**
     * All the user roles are prepended by U
     *
     */
    const ROLE_TYPE_USER = 'U';
    
    /**
     * Permission level to deny access
     *
     */
    const RULE_PERM_DENY = 0;
    
    /**
     * Permission level to inheric access from parent role
     *
     */
    const RULE_PERM_INHERIT = 1;
    
    /**
     * Permission level to allow access
     *
     */
    const RULE_PERM_ALLOW = 2;
    
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
    /**
     * Get role registry object or create one
     *
     * @return Mage_Admin_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = Mage::getModel('cssubaccount/acl_group_registry');
        }
        return $this->_roleRegistry;
    }
    
    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role $role
     * @param Zend_Acl_Role $parent
     * @return Mage_Admin_Model_Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
	
	/**
     * Check current vendor permission on resource and privilege
     *
     * Mage::getSingleton('csgroup/observer')->isAllowed('csmarketplace')
     * Mage::getSingleton('csgroup/observer')->isAllowed('csmarketplace/vendor')
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowedResource($resource, $privilege=null)
    {
		$session = $this->_getSession();
        $vendor = $session->getVendor();
        $acl = $session->getAcl();
		
        if ($vendor && $acl) {
            if (!preg_match('/^vendor/', $resource)) {
                $resource = 'vendor/'.$resource;
            }
			$resource = trim($resource,'/');

            try {
				/* print_r($acl->getResources(true));die; */
				if($acl->has($resource)) {
					return $acl->isAllowed($vendor->getAclGroup(), $resource, $privilege);
				} else {
					$tmp = explode('/',$resource);
					$newtmp = '/'.preg_quote(end($tmp)).'/i';
					$resource = preg_replace($newtmp,'',$resource);
					return $this->isAllowedResource($resource, $privilege);
				}
            } catch(Zend_Acl_Exception $e) {
				try {
                    if (!$acl->has($resource)) {
						return $acl->isAllowed($vendor->getAclGroup(), null, $privilege);
					}
                } catch (Exception $e) { 
					return false;
				}
			} catch (Exception $e) {
                return false;
            }
        } elseif(!$vendor) {
			return true;
		} elseif ($vendor && !$acl) {
			return true;
		}
        return false;
    }

	/**
     * Find first resource item that vendor is able to access
     *
	 */
    public function findFirstAvailableResource()
    {
		$url = '*/*/denied';
        $session = $this->_getSession();
        $vendor = $session->getSubVendorData();
        $acl = $vendor['role'];
		die($acl);die;
		foreach($acl->getResources(true) as $resourceId => $resource) {
			if($resourceId != 'all' && $acl->isAllowedResource($resourceId) && count($resource['children']) == 0) {
				$url = explode('/',$resourceId);
				unset($url[0]);
				$url = implode('/',array_values($url));
				if( strlen($url) > 0 ) break;
			}
		}
        return $url;
    }
	
	/**
     * @return array of registered resources
     */
    public function getResources($all = false)
    {	if($all) return $this->_resources;
        return array_keys($this->_resources);
    }
}