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

class Ced_CsSubAccount_Model_Available extends Ced_CsMarketplace_Model_Abstract
{
    
    /**
     * 
     * 
     * @return Ced_CsSubAccount_Model_Available
     */
	
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * 
     * @return Ambigous <object, boolean, Mage_Core_Model_Abstract, false>
     */
    /* public function getVendorsCollection()
    {
        return Mage::getResourceModel('csgroup/groups_vendor_collection');
    } */

    /**
     * 
     * @return Ambigous <unknown, number, NULL, string, Varien_Simplexml_Element>
     */
    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, null, null, true);
    }

    /**
     * 
     * @return Ambigous <unknown, number, NULL, string, Varien_Simplexml_Element>
     */
    public function getResourcesList()
    {
        return $this->_buildResourcesArray();
    }

    /**
     * 
     * @return Ambigous <unknown, number, NULL, string, Varien_Simplexml_Element>
     */
    public function getResourcesList2D()
    {
        return $this->_buildResourcesArray(null, null, null, true);
    }

    /**
     * getting group vendors
     */
   /*  public function getGroupVendors()
    {
        return $this->getResource()->getGroupVendors($this);
    } */

    /**
     * 
     * @param Varien_Simplexml_Element $resource
     * @param string $parentName
     * @param number $level
     * @param string $represent2Darray
     * @param string $rawNodes
     * @param string $module
     * @return Varien_Simplexml_Element|Ambigous <NULL, string>|unknown|Ambigous <number, NULL, string>
     */
    protected function _buildResourcesArray(Varien_Simplexml_Element $resource=null, $parentName=null, $level=0, $represent2Darray=null, $rawNodes = false, $module = 'adminhtml')
    {
        static $result;
        if (is_null($resource)) {
            $resource = Mage::getSingleton('cssubaccount/config')->getVendorConfig()->getNode('acl/resources');
			$resourceName = null;
            $level = -1;
        } else {
            $resourceName = $parentName;
            if ($resource->getName()!='title' && $resource->getName()!='sort_order' && $resource->getName() != 'children' && $resource->getName() != 'resource_name') {
                
				if(isset($resource->resource_name) && strlen((string)$resource->resource_name) > 0) {
					$resourceNameTemp = trim((string)$resource->resource_name);
					if (!preg_match('/^vendor/', $resourceNameTemp)) {
						$resourceNameTemp = 'vendor/'.$resourceNameTemp;
					}
					$resourceNameTemp = trim($resourceNameTemp,'/');
					if($resource->resource_name->getAttribute('ifconfig')) {						
						if(Mage::getStoreConfig((string)$resource->resource_name->getAttribute('ifconfig'))) {
							$resourceName = $resourceNameTemp;
						} else {
							$resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
						}
					} else {
						$resourceName = $resourceNameTemp;
					}
				} else {
					$resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
				}
				
                //assigning module for its' children nodes
                if ($resource->getAttribute('module')) {
                    $module = (string)$resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute("aclpath", $resourceName);
                    $resource->addAttribute("module_c", $module);
                }

               if ( is_null($represent2Darray) ) {
                    $result[$resourceName]['name']  = Mage::helper($module)->__((string)$resource->title);
                    $result[$resourceName]['level'] = $level;
                } else {
                    $result[] = $resourceName;
                }
            }
        }

        $children = $resource->children();
        
        if (empty($children)) { 
            if ($rawNodes) {
                return $resource;
            } else {
                return $result;
            }
        }
        foreach ($children as $child) {
            $this->_buildResourcesArray($child, $resourceName, $level+1, $represent2Darray, $rawNodes, $module);
        }
        if ($rawNodes) {
        	
            return $resource;
        } else {
        	/* if(array_key_exists('vendor/csmarketplace/vendor/index', $result))
        		unset($result['vendor/csmarketplace/vendor/index']); */
            return $result;
        }
    }

}
