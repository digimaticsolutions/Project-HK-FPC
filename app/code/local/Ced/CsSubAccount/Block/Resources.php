
<!--
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
  * @author   	 CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
-->

<?php 

class Ced_CsSubAccount_Block_Resources extends Ced_CsMarketplace_Block_Vendor_Abstract
{
	
	
	/**
	 * Whether tab is available
	 *
	 * @return bool
	 */
	/* public function _construct(){
		print_r(Mage::getConfig()->getNode('vendor/acl'));die('llll');
	} */
	public function canShowTab()
	{
		return true;
	}
	
	/**
	 * Whether tab is visible
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return false;
	}
	
	/**
	 * Class constructor
	 *
	 */
	
	
	public function __construct()
	{ 
		$this->_objectId = 'id';
		$this->_controller = 'customer';
		parent::__construct();
		$this->setId('subVendor_resource');
		
		//$groupCode = ( strlen($this->getRequest()->getParam('gcode')) > 0 ) ? $this->getRequest()->getParam('gcode') : Mage::registry('GCODE');
	
		$resources = (array)Mage::getModel('cssubaccount/available')->getResourcesList();
		//die('klkl');
		/* if(array_key_exists('vendor/csmarketplace/vendor/index', $resources)){ //die('oooo');
			//$index = array_search('vendor/csmarketplace/vendor/index', $resources);
			//echo $index;die;
			unset($resources['vendor/csmarketplace/vendor/index']);
		} */
		//print_r($resources);die('ooooo');
		
	
		$role = Mage::getModel('cssubaccount/cssubaccount')->load($this->getRequest()->getParam('id'))->getRole();
		$roles = explode(',', $role);
		//print_r($roles);die('lklklk');
		/* if($group && $group->getId()) {
			$groupId = $group->getId();
		} */
	
		//$rules_set = Mage::getResourceModel('csgroup/rules_collection')->getByGroups($groupId)->load();
	
		/* $selrids = array();
	
		foreach ($rules_set->getItems() as $item) {
			$itemResourceId = $item->getResource_id();
			if (array_key_exists(strtolower($itemResourceId), $resources) && $item->getPermission() == 'allow') {
				$resources[$itemResourceId]['checked'] = true;
				array_push($selrids, $itemResourceId);
			}
		}  */
	
		$this->setSelectedResources($roles); 
	
		//$this->setTemplate('csgroup/groupsedit.phtml');
		//->assign('resources', $resources);
		//->assign('checkedResources', join(',', $selrids));
	}
	
	//Save Resources and back button layout
	protected function _prepareLayout()
	{
		$this->setChild('back_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label'     => Mage::helper('cssubaccount')->__('Back'),
						'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/list', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
						'class' => 'btn btn-info uptransform'
				))
		);
			
		$this->setChild('send_button',
				$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
						'label'     => Mage::helper('cssubaccount')->__('Save'),
						//'onclick'   => 'setLocation(\''.Mage::getUrl('*/*/send', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
						'class' => 'save scalable btn btn-success uptransform'
				))
		);
			
		return parent::_prepareLayout();
	}
	
	
	public function getHeader()
	{
		return	$this->helper('cssubaccount')->__('Assign Resources To Sub - Vendors');
	}
	/**
	 * Check if everything is allowed
	 *
	 * @return boolean
	 */
	public function getEverythingAllowed()
	{
		return in_array('all', $this->getSelectedResources());
	}
	
	/**
	 * Get Json Representation of Resource Tree
	 *
	 * @return string
	 */
	public function getResTreeJson()
	{
		$rid = Mage::app()->getRequest()->getParam('id');
		$resources = Mage::getModel('cssubaccount/available')->getResourcesTree();
		//print_r($resources);die('lll');
		$rootArray = $this->_getNodeJson($resources->vendor, 1);
		//return print_r($rootArray,true);
		$json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
		return $json;
	}
	
	/**
	 * Compare two nodes of the Resource Tree
	 *
	 * @param array $a
	 * @param array $b
	 * @return boolean
	 */
	protected function _sortTree($a, $b)
	{
		return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
	}
	
	/**
	 * Get Node Json
	 *
	 * @param mixed $node
	 * @param int $level
	 * @return array
	 */
	protected function _getNodeJson($node, $level = 0)
	{
		$item = array();
		$selres = $this->getSelectedResources();
	
		if ($level != 0) {
			$item['text'] = Mage::helper('adminhtml')->__((string)$node->title);
			$item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
			$item['id'] = (string)$node->attributes()->aclpath;
	
			if (in_array($item['id'], $selres))
				$item['checked'] = true;
		}
		if (isset($node->children)) {
			$children = $node->children->children();
		} else {
			$children = $node->children();
		}
		if (empty($children)) {
			return $item;
		}
	
		if ($children) {
			$item['children'] = array();
			//$item['cls'] = 'fiche-node';
			foreach ($children as $child) {
				if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
					if (!(string)$child->title) {
						continue;
					}
					if ($level != 0) {
						$item['children'][] = $this->_getNodeJson($child, $level+1);
					} else {
						$item = $this->_getNodeJson($child, $level+1);
					}
				}
			}
			if (!empty($item['children'])) {
				usort($item['children'], array($this, '_sortTree'));
			}
		}
		return $item;
	}
}