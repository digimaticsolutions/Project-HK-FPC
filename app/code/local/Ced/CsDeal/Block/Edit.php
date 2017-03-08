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
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author 	   CedCommerce Core Team <coreteam@cedcommerce.com>
 */
class Ced_CsDeal_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('csdeal/edit.phtml');
        $this->setId('deal_edit');
                $this->_removeButton("back");
                $this->_removeButton("save");
                $this->_removeButton('reset');
                
                $this->_addButton("deal_delete", array(
                    "label"     => Mage::helper("csdeal")->__("Delete"),
                    "onclick"   => "dealdelete()",
                    "class"     => "btn btn-danger",
                ));
               
                 $this->_addButton("deal_back", array(
                    "label"     => Mage::helper("csdeal")->__("Back"),
                    "onclick"   => "dealback()",
                    "class"     => "btn btn-warning",
                ));
                 $this->_addButton("deal_save", array(
                    "label"     => Mage::helper("csdeal")->__("Save Deal"),
                    "onclick"   => "save()",
                    "class"     => "btn btn-success ",
                ));
                $this->_formScripts[] = "

                           function save(){
                                edit_form.submit($('edit_form').action);
                            }
                            function dealdelete(){
                                document.location.href='".Mage::getUrl('csdeal/deal/delete',array('deal_id' => Mage::registry("csdeal_data")->getDealId()))."'
                            }
                            function dealback(){
                                document.location.href='".Mage::getUrl('csdeal/deal/list')."'
                            }
                        ";
        }

        public function getHeaderText()
        {
                if( Mage::registry("csdeal_data") && Mage::registry("csdeal_data")->getDealId() ){

                    return Mage::helper("csdeal")->__("Edit Deal ' %s '", $this->htmlEscape(Mage::registry("csdeal_data")->getDealId()));

                } 
        }
        public function getSaveUrl()
        {
            return Mage::getUrl('csdeal/deal/save/deal_id/'.Mage::registry("csdeal_data")->getDealId());
        }
     
       
	
}
