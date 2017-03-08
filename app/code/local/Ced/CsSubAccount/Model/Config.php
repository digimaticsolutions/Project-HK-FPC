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
class Ced_CsSubAccount_Model_Config extends Varien_Simplexml_Config
{
    /**
     * vendor.xml merged config
     *
     * @var Varien_Simplexml_Config
     */
    protected $_vendorConfig;

    /**
     * Load config from merged vendor.xml files
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCacheId('vendor_acl_menu_config');
        /* @var $vendorConfig Varien_Simplexml_Config */
        $vendorConfig = Mage::app()->loadCache($this->getCacheId());
        if ($vendorConfig) {
            $this->_vendorConfig = new Varien_Simplexml_Config($vendorConfig);
        } else {
            $vendorConfig = new Varien_Simplexml_Config;
            $vendorConfig->loadString('<?xml version="1.0"?><config></config>');
            Mage::getConfig()->loadModulesConfiguration('vendor.xml', $vendorConfig);
            $this->_vendorConfig = $vendorConfig;

            /**
             * @deprecated after 1.4.0.0-alpha2
             * support backwards compatibility with config.xml
             */
            $aclConfig  = Mage::getConfig()->getNode('vendor/acl');
            if ($aclConfig) {
                $vendorConfig->getNode()->extendChild($aclConfig, true);
            }
            $menuConfig = Mage::getConfig()->getNode('vendor/menu');
            if ($menuConfig) {
                $vendorConfig->getNode()->extendChild($menuConfig, true);
            }

            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($vendorConfig->getXmlString(), $this->getCacheId(),
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
    }

    /**
     * Load Acl resources from config
     *
     * @param Ced_CsGroup_Model_Acl $acl
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Ced_CsGroup_Model_Config
     */
    public function loadAclResources(Ced_CsSubAccount_Model_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = $this->getVendorConfig()->getNode("acl/resources");
            $resourceName = null;
        } else {
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
            $acl->add(Mage::getModel('cssubaccount/acl_resource', $resourceName), $parentName);
        }

        if (isset($resource->all)) {
            $acl->add(Mage::getModel('cssubaccount/acl_resource', 'all'), null);
        }

        if (isset($resource->vendor)) {
            $children = $resource->vendor;
        } elseif (isset($resource->children)){
            $children = $resource->children->children();
        }



        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            $this->loadAclResources($acl, $res, $resourceName);
        }
        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
     */
    public function getAclAssert($name='')
    {
        $asserts = $this->getNode("vendor/acl/asserts");
        if (''===$name) {
            return $asserts;
        }

        if (isset($asserts->$name)) {
            return $asserts->$name;
        }

        return false;
    }

    /**
     * Retrieve privilege set by name
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
     */
    public function getAclPrivilegeSet($name='')
    {
        $sets = $this->getNode("vendor/acl/privilegeSets");
        if (''===$name) {
            return $sets;
        }

        if (isset($sets->$name)) {
            return $sets->$name;
        }

        return false;
    }

    /**
     * Retrieve xml config
     *
     * @return Varien_Simplexml_Config
     */
    public function getVendorConfig()
    {
        return $this->_vendorConfig;
    }

    /**
     * Get menu item label by item path
     *
     * @param string $path
     * @return string
     */
    public function getMenuItemLabel($path)
    {
        $moduleName = 'vendor';
        $menuNode = $this->getVendorConfig()->getNode('menu/' . str_replace('/', '/children/', trim($path, '/')));
        if ($menuNode->getAttribute('module')) {
            $moduleName = (string)$menuNode->getAttribute('module');
        }
        return Mage::helper($moduleName)->__((string)$menuNode->title);
    }
}
