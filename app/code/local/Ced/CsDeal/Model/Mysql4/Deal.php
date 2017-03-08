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
 * @package     Ced_OrderDelete
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 **/
class Ced_CsDeal_Model_Mysql4_Deal extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("csdeal/deal", "deal_id");
    }
    public function getVendorDealProductIds($id) {
    		$DealTable = $this->getTable('csdeal/deal');
			$dbh    = $this->_getReadAdapter();
            $select = $dbh->select()->from($DealTable,array('product_id'))
                ->where("vendor_id ={$id}");
            return $dbh->fetchCol($select);
	}
    public function getDealProductIds() {
            $DealTable = $this->getTable('csdeal/deal');
            $dbh    = $this->_getReadAdapter();
            $select = $dbh->select()->from($DealTable,array('product_id'));
            return $dbh->fetchCol($select);
    }

}
