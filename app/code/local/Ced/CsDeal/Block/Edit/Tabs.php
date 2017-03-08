<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_CsProduct
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Edit tabs block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsDeal_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'csdeal/edit_tab_form';

    public function __construct()
    {
        parent::__construct();
        $this->setId('deal_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('csdeal')->__('Deal Info'));
        $this->setTemplate('csdeal/edit/tabs.phtml');
    }

    protected function _prepareLayout()
    {
            $this->addTab('deal',  array(
                    'label'     => Mage::helper('csdeal')->__('Edit Deal'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('csdeal/edit_tab_deal')->setTemplate('csdeal/edit/deal/tab/deal.phtml')->toHtml()),
             ));
            
            
            
         
        return parent::_prepareLayout();
    }

    /**
     * Translate html content
     * 
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
