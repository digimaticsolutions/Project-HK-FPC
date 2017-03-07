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
  * @package     Ced_CsProduct
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

/**
 * catalog super product configurable tab
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Super_Config extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config
{
    

    /**
     * Prepare Layout data
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config
     */
    protected function _prepareLayout()
    {
    	parent::_prepareLayout();
    	$this->setChild('grid',
            $this->getLayout()->createBlock('csproduct/edit_tab_super_config_grid')
        );

        $this->setChild('create_empty',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Create Empty'),
                    'class' => 'add',
                    'onclick' => 'superProduct.createEmptyProduct()'
                ))
        );

        if ($this->_getProduct()->getId()) {
            $this->setChild('simple',
                $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config_simple')
            );

            $this->setChild('create_from_configurable',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('catalog')->__('Copy From Configurable'),
                        'class' => 'add',
                        'onclick' => 'superProduct.createNewProduct()'
                    ))
            );
        }

        return $this;
    }
    
    /**
     * Retrieve Create New Empty Product URL
     *
     * @return string
     */
    public function getNewEmptyProductUrl()
    {
    	return Mage::getUrl(
    			'*/*/new',
    			array(
    					'set'      => $this->_getProduct()->getAttributeSetId(),
    					'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    					'required' => $this->_getRequiredAttributesIds(),
    					'popup'    => 1
    			)
    	);
    }
    
    /**
     * Retrieve Create New Product URL
     *
     * @return string
     */
    public function getNewProductUrl()
    {
    	return Mage::getUrl(
    			'*/*/new',
    			array(
    					'set'      => $this->_getProduct()->getAttributeSetId(),
    					'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    					'required' => $this->_getRequiredAttributesIds(),
    					'popup'    => 1,
    					'product'  => $this->_getProduct()->getId()
    			)
    	);
    }
    
    /**
     * Retrieve Quick create product URL
     *
     * @return string
     */
    public function getQuickCreationUrl()
    {
    	return Mage::getUrl(
    			'*/*/quickCreate',
    			array(
    					'product'  => $this->_getProduct()->getId()
    			)
    	);
    }

}
