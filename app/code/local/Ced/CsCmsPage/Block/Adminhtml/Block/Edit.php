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

class Ced_CsCmsPage_Block_Adminhtml_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 * 
	 * preaparing layout before rendering
	 * @see Mage_Adminhtml_Block_Widget_Form_Container::_prepareLayout()
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
	}
	/**
	 * @return: vendor Blocks
	 */
	public function getBlock(){
		$Collection = '';
		if($this->getRequest()->getParam('block_id')>0){
			$block_id = $this->getRequest()->getParam('block_id');
			$Collection = Mage::getModel('cscmspage/block')->load($block_id);
		}
		return $Collection;
	}
	/**
	 * @return:Block Store
	 */
	public function getBlockstore(){
		$VendorBlockStore =array();
		if($this->getRequest()->getParam('block_id')>0){
			$block_id = $this->getRequest()->getParam('block_id');
			$CmsBlockPage = Mage::getModel('cscmspage/vendorblock')->getCollection()
										->addFieldToFilter('block_id',$block_id);
				
			foreach	($CmsBlockPage as $block){
				$VendorBlockStore[] = $block->getStoreId();	
			}
		}
		return $VendorBlockStore;
	}
	public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_blockGroup = 'cscmspage';
        $this->_controller = 'adminhtml_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('cscmspage')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('cscmspage')->__('Delete Block'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
		if ($model = Mage::getModel('cscmspage/block')->load($this->getRequest()->getParam('block_id'))) {
            return Mage::helper('cscmspage')->__("Edit Block '%s'", $this->escapeHtml($model->getTitle()));
        }
        else {
            return Mage::helper('cscmspage')->__('New Block');
        }
    }
	/**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('adminhtml/adminhtml_vendorblock/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }	
}
