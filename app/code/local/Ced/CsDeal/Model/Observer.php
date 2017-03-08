<?php
 //require_once('app/Mage.php');
class Ced_CsDeal_Model_Observer
{
    function insertDealBlock($observer)
    {
        $_block = $observer->getBlock();
        $_type = $_block->getType();

        if ($_type == 'catalog/product_price') {

            if(!Mage::registry('view_id_deal_in')){
                $execute=true;
            }

            if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='catalog_product_view' && $execute){
                Mage::register('view_id_deal_in','7');
                $_child = clone $_block;
                $_child->setType('core/template');
                $_block->setChild('child'.$_block->getProduct()->getId(), $_child);
                $_block->setTemplate('csdeal/show/deal.phtml');
            }else if(Mage::app()->getFrontController()->getAction()->getFullActionName()=='csmarketplace_vshops_view' || Mage::app()->getFrontController()->getAction()->getFullActionName()=='catalog_category_view'){
                $_child = clone $_block;
                $_child->setType('core/template');
                $_block->setChild('child'.$_block->getProduct()->getId(), $_child);
                $_block->setTemplate('csdeal/show/deal.phtml');
            }   
        }
    }
    
    public function deleteDealProduct($observer)
    {
        $deal = $observer->getDeal();
        $product=Mage::getModel('catalog/product')->load($deal->getProductId());
        try{
        $product->setSpecialPrice('');
        $product->setSpecialFromDate('');
        $product->setSpecialFromDateIsFormated(true);
        $product->setSpecialToDate('');
        $product->setSpecialToDateIsFormated(true);
        $product->save();
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
        } 
    }public function deleteProduct($observer)
    {
        $product = $observer->getProduct();
        $deal=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('product_id',$product->getId())->getFirstItem();
        try{
        $deal->delete();
        $deal->save();
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
        } 
    }
    public function updateDealProduct($observer)
    {
        $deal = $observer->getDeal();
        $product=Mage::getModel('catalog/product')->load($deal->getProductId());
        try{
        $product->setSpecialPrice($deal->getDealPrice());
        $product->setSpecialFromDate($deal->getStartDate());
        $product->setSpecialFromDateIsFormated(true);
        $product->setSpecialToDate($deal->getEndDate());
        $product->setSpecialToDateIsFormated(true);
        Mage::log($product,'1','mydeal.log',TRUE);  
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);   
        $product->save();
        }catch(Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
        } 
    }
    public function productSaveAfter($observer)
    {
        $product = $observer->getProduct();
        $deal=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('product_id',$product->getId())->getFirstItem();
        $dealPro=Mage::getModel('catalog/product')->load($product->getId());
        if($deal->getDealId()){
         try{
            $price=$dealPro->getSpecialPrice();
            $fromDate=$dealPro->getSpecialFromDate();
            $toDate=$dealPro->getSpecialToDate();
            if($price){
               $deal->setEndDate($toDate); 
               $deal->setStartDate($fromDate);
               $deal->save();
            }
            }catch(Exception $e){
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
            }    
        }
        
    }
    
    public function setExpireMethod()
    {
        $productIds=Mage::getModel('csdeal/deal')->getDealProductIds();
        $cur_time   =  date('Y-m-d');
        foreach ($productIds as $productId) {
           $model=Mage::getModel('csdeal/deal')->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();
           $end_date = date_create($model->getData('end_date'));
           $curr_date= date_create($cur_time);
           $interval = date_diff($curr_date,$end_date,false);
           if ($interval->format('%a')<=0) {
                try{
                    $product=Mage::getModel('catalog/product')->load($productId);
                    $product->setSpecialPrice('');
                    $product->setSpecialFromDate('');
                    $product->setSpecialFromDateIsFormated(true);
                    $product->setSpecialToDate('');
                    $product->setSpecialToDateIsFormated(true);
                    $product->save();
                    }catch(Exception $e){
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%s',$e->getMessage()));
                    } 
           }
        
        }
    }

}
