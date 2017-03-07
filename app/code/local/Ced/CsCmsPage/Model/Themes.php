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

class Ced_CsCmsPage_Model_Themes extends Mage_Core_Model_Abstract
{
	public function toOptionArray($withEmpty = true)
	{
		if (is_null($this->_options)) {
			$design = Mage::getModel('core/design_package')->getThemeList();
			$options = array();
			foreach ($design as $package => $themes){
				if ($package == 'ced') continue;
				$packageOption = array('label' => $package);
				$themeOptions = array();
				foreach ($themes as $theme) {
					$themeOptions[] = array(
							'label' => ($this->getIsFullLabel() ? $package . ' / ' : '') . $theme,
							'value' => $package . '/' . $theme
					);
				}
				$packageOption['value'] = $themeOptions;
				$options[] = $packageOption;
			}
			$this->_options = $options;
		}
		$options = $this->_options;
		if ($withEmpty) {
			array_unshift($options, array(
			'value'=>'',
			'label'=>Mage::helper('core')->__('-- Please Select --'))
			);
		}
		return $options;
	}
}