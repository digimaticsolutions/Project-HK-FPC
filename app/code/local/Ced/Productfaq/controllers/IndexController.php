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
  * @package     Ced_Productfaq
  * @author   CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
class Ced_Productfaq_IndexController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Save Product Likes and increments count
	 *
	 * @return int
	 */
	public function likeAction() {
		if ($this->getRequest ()->isPost ()) {
			$questionid = $this->getRequest ()->getParam ( 'id' );
			$userip = $this->getRequest ()->getParam ( 'ip' );
			// $userip=serialize($userip);
			$productid = $this->getRequest ()->getParam ( 'productid' );
			//print_r($productid);echo'<br><br>';
			//die('--here');
			$likes = Mage::getModel ( 'productfaq/like' )->getCollection ()->addFieldToFilter ( 'question_id', $questionid )->addFieldToFilter ( 'product_id', $productid );
			if ($likes) {
				//die($likes->getData());die('--check');
				foreach ( $likes as $data ) {
					$id = $data ['id'];
					$count = $data ['likes'];
					$ips = $data ['user_ip'];
				}
				//print_r($id.'--'.$count.'---'.$ips);die('--check');
				$ips = unserialize ( $ips );
				if ($ips) {
					$ips .= ',' . $userip;
				} else {
					$ips .= $userip;
				}
				$userip = serialize ( $ips );
				$count ++;
				if ($questionid) {
					$data = array (
							'question_id' => $questionid,
							'product_id' => $productid,
							'user_ip' => $userip,
							'likes' => $count 
					);
					$model = Mage::getModel ( 'productfaq/like' )->load ( $id )->addData ( $data );
					;
					$model->setId ( $id )->save ();
					$result ['count'] = $count;
					$result ['like'] = $this->__ ( 'unlike' );
				}
				
				$this->getResponse ()->setHeader ( 'Content-type', 'application/json' );
				$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
			}
		}
	}
	
	/**
	 * Save Customer Question
	 *
	 * @return string
	 */
	public function saveAction() {
		$email = $this->getRequest ()->getParam ( 'email' );
		$question = $this->getRequest ()->getParam ( 'question' );
		$prdctid = $this->getRequest ()->getParam ( 'product' );
		$faq = Mage::getModel ( 'productfaq/productfaq' );
		if ($faq) {
			$faq->setData ( 'email_id', $email )->setData ( 'title', $question )->setData ( 'product_id', $prdctid )->setData ( 'visible_on_frontend', 0 );
			$faq->save ();
			echo $this->__ ( 'Your Question Was Successfully Posted.We will Answer You Shortly.' );
		}
	}
	
	/**
	 * Decrement Product likes count
	 *
	 * @return int
	 */
	public function unlikeAction() {
		if ($this->getRequest ()->isPost ()) {
			$questionid = $this->getRequest ()->getParam ( 'id' );
			$userip = $this->getRequest ()->getParam ( 'ip' );
			$productid = $this->getRequest ()->getParam ( 'productid' );
			/*
			 * $model = Mage::getModel('productfaq/like')->getCollection()->addFieldToFilter('question_id',$questionid)->addFieldToFilter('user_ip',$userip); foreach ($model as $del) { $id=$del->getData('id'); $del->setId($id)->delete(); $result['unlike']='like'; }
			 */
			$likes = Mage::getModel ( 'productfaq/like' )->getCollection ()->addFieldToFilter ( 'question_id', $questionid )->addFieldToFilter ( 'product_id', $productid );
			if ($likes) {
				foreach ( $likes as $data ) {
					$id = $data ['id'];
					$count = $data ['likes'];
					$ips = $data ['user_ip'];
				}
				
				$ips = unserialize ( $ips );
				
				if (strpos ( $ips, $userip ) !== false) {
					if (strpos ( $ips, ',' ) !== false) {
						$userip = str_replace ( $userip . ',', "", $ips );
					} else {
						$userip = str_replace ( $userip, "", $ips );
					}
				}
				$userip = serialize ( $userip );
				$count --;
				if($count < 0)
					$count = 0;
				$data = array (
						'likes' => $count,
						'user_ip' => $userip 
				);
				$model = Mage::getModel ( 'productfaq/like' )->load ( $id )->addData ( $data );
				;
				$model->setId ( $id )->save ();
				
				$result ['count'] = $count;
				$result ['unlike'] = $this->__ ( 'like' );
				$this->getResponse ()->setHeader ( 'Content-type', 'application/json' );
				$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
			}
		}
	}
}
