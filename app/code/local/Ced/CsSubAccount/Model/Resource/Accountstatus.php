<?php
class Ced_CsSubAccount_Model_Resource_Accountstatus extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('cssubaccount/accountstatus', 'id');
	}
}