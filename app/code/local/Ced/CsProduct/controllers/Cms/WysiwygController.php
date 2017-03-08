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

class Ced_CsProduct_Cms_WysiwygController extends Ced_CsMarketplace_Controller_AbstractController
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('core')->urlDecode($directive);
        $url = Mage::getModel('core/email_template_filter')->filter($directive);
        try {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open($url);
            $image->display();
        } catch (Exception $e) {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open(Mage::getSingleton('cms/wysiwyg_config')->getSkinImagePlaceholderUrl());
            $image->display();
            /*
            $image = imagecreate(100, 100);
            $bkgrColor = imagecolorallocate($image,10,10,10);
            imagefill($image,0,0,$bkgrColor);
            $textColor = imagecolorallocate($image,255,255,255);
            imagestring($image, 4, 10, 10, 'Skin image', $textColor);
            header('Content-type: image/png');
            imagepng($image);
            imagedestroy($image);
            */
        }
    }
}
