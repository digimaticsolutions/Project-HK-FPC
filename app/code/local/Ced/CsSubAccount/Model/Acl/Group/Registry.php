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
class Ced_CsSubAccount_Model_Acl_Group_Registry extends Zend_Acl_Role_Registry 
{
    /**
     * Add parent to the $group node
     *
     * @param Zend_Acl_Role_Interface|string $group
     * @param array|Zend_Acl_Role_Interface|string $parents
     * @return Mage_Auth_Model_Acl_Role_Registry
     */
    function addParent($group, $parents)
    {
        try {
            if ($group instanceof Zend_Acl_Role_Interface) {
                $groupId = $group->getGroupId();
            } else {
                $groupId = $group;
                $group = $this->get($group);
            }
        } catch (Zend_Acl_Role_Registry_Exception $e) {
            throw new Zend_Acl_Role_Registry_Exception("Child Group id '$groupId' does not exist");
        }
        
        if (!is_array($parents)) {
            $parents = array($parents);
        }
        foreach ($parents as $parent) {
            try {
                if ($parent instanceof Zend_Acl_Role_Interface) {
                    $groupParentId = $parent->getGroupId();
                } else {
                    $groupParentId = $parent;
                }
                $groupParent = $this->get($groupParentId);
            } catch (Zend_Acl_Role_Registry_Exception $e) {
                throw new Zend_Acl_Role_Registry_Exception("Parent Group id '$groupParentId' does not exist");
            }
            $this->_roles[$groupId]['parents'][$groupParentId] = $groupParent;
            $this->_roles[$groupParentId]['children'][$groupId] = $group;
        }
        return $this;
    }
}
