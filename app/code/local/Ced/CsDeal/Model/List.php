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
 * @package     Ced_CsMembership
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Membership Model Status
 *
 * @category    Ced
 * @package     Ced_CsMembership
 * @author      CedCommerce Magento Core Team <magentocoreteam@cedcommerce.com>
 */
class Ced_CsDeal_Model_List extends Varien_Object
{
    
    const PRODUCT_VIEW= 'view';
    const BOTH  = 'both';
    const CATEGORY_PAGE  = 'list';
    static public function toOptionArray()
    {
        return array(
            self::BOTH      => Mage::helper('csdeal')->__('List and View both'),
            self::CATEGORY_PAGE  => Mage::helper('csdeal')->__('only List Page'),
            self::PRODUCT_VIEW   => Mage::helper('csdeal')->__('only View Page'),
        );
    }
    
}