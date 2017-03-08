<?php 

class Ced_CsSubAccount_Block_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{

	public function render(Varien_Object $row)
	{ 	
		
		$collection = Mage::getModel('cssubaccount/cssubaccount')->load($row->getId())->getStatus();
		
		if($collection==1)
			return 'Approved';
		if($collection==2)
			return 'Disapproved';
		else
			return 'Pending';
	}

}