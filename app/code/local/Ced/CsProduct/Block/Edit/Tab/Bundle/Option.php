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
 * Bundle option renderer
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */

class Ced_CsProduct_Block_Edit_Tab_Bundle_Option extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option
{

    /**
     * Retrieve list of bundle product options
     *
     * @return array
     */
    public function getOptions()
    {
    	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        if (!$this->_options) {
            $this->getProduct()->getTypeInstance(true)->setStoreFilter($this->getProduct()->getStoreId(),
                $this->getProduct());

            $optionCollection = $this->getProduct()->getTypeInstance(true)->getOptionsCollection($this->getProduct());

            $selectionCollection = $this->getProduct()->getTypeInstance(true)->getSelectionsCollection(
                $this->getProduct()->getTypeInstance(true)->getOptionsIds($this->getProduct()),
                $this->getProduct()
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection);
            if ($this->getCanReadPrice() === false) {
                foreach ($this->_options as $option) {
                    if ($option->getSelections()) {
                        foreach ($option->getSelections() as $selection) {
                            $selection->setCanReadPrice($this->getCanReadPrice());
                            $selection->setCanEditPrice($this->getCanEditPrice());
                        }
                    }
                }
            }
        }
        return $this->_options;
    }

  
}
