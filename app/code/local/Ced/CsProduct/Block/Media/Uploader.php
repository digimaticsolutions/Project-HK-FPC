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
 * Adminhtml media library uploader
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ced_CsProduct_Block_Media_Uploader extends Mage_Adminhtml_Block_Media_Uploader
{

    protected $_config;

    public function __construct()
    {
        parent::__construct();
        $this->setId($this->getId() . '_Uploader');
        $this->setTemplate('csproduct/media/uploader.phtml');
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters(array(
            'images' => array(
                'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                'files' => array('*.gif', '*.jpg', '*.png')
            ),
            'media' => array(
                'label' => Mage::helper('adminhtml')->__('Media (.avi, .flv, .swf)'),
                'files' => array('*.avi', '*.flv', '*.swf')
            ),
            'all'    => array(
                'label' => Mage::helper('adminhtml')->__('All Files'),
                'files' => array('*.*')
            )
        ));
    } 
    
    /**
     * Retrive full uploader SWF's file URL
     * Implemented to solve problem with cross domain SWFs
     * Now uploader can be only in the same URL where backend located
     *
     * @param string url to uploader in current theme
     * @return string full URL
     */
    public function getUploaderUrl($url)
    {
    	if (!is_string($url)) {
    		$url = '';
    	}
    	$design = Mage::getDesign();
    	$theme = $design->getTheme('skin');
    	if (empty($url) || !$design->validateFile($url, array('_type' => 'skin', '_theme' => $theme))) {
    		$theme = $design->getDefaultTheme();
    	}
		
		if($design->getPackageName()=="ced")
			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) .'skin/' .$design->getArea() . '/' . $design->getPackageName() . '/' . $theme . '/' . $url;
		else 
			return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)  .'skin/' .
		'frontend/base/default/images/ced/csproduct/'. $url;
    }
}
