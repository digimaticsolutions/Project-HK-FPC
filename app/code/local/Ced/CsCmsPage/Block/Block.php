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

class Ced_CsCmsPage_Block_Block extends Mage_Core_Block_Abstract
{
    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';
        if ($blockId) {
            $block = Mage::getModel('cscmspage/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $helper = Mage::helper('cscmspage');
                $processor = $helper->getBlockTemplateProcessor();
                $html = $processor->filter($block->getContent());
                //$this->addModelTags($block);
            }
        }
        return $html;
    }
}
