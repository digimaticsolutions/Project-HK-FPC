<?php
class Ced_CsSubAccount_Model_Resource_Cssubaccount extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('cssubaccount/cssubaccount', 'id');
	}
}