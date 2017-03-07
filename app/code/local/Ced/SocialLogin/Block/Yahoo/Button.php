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

 * @package     Ced_SocialLogin

 * @author 		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>

 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */



/**

 * SocialLogin 	Yahoo/Button block

 *

 * @category   	Ced

 * @package    	Ced_SocialLogin

 * @author		CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>

 */





class Ced_SocialLogin_Block_Yahoo_Button extends Mage_Core_Block_Template

{

    protected $client = null;

    protected $userInfo = null;

    protected $redirectUri = null;



    protected function _construct() {

        parent::_construct();



        $this->client = Mage::getSingleton('sociallogin/yahoo_client');

        if(!($this->client->isEnabled())) {

            return;

        }



        $this->userInfo = Mage::registry('ced_sociallogin_yahoo_userdetails');

        

        // CSRF protection
		
		if(!Mage::getSingleton('core/session')->getYahooCsrf() || Mage::getSingleton('core/session')->getYahooCsrf()=='') {
		
			$csrf = md5(uniqid(rand(), TRUE));
	
			Mage::getSingleton('core/session')->setYahooCsrf($csrf);
		} else {
			$csrf = Mage::getSingleton('core/session')->getYahooCsrf();
		}
			$this->client->setState($csrf);
		

        
        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {

            $redirect = Mage::helper('core/url')->getCurrentUrl();      

        }        

        

        // Redirect uri
        Mage::getSingleton('core/session')->setYahooRedirect($redirect);        



        $this->setTemplate('ced/sociallogin/yahoo/button.phtml');

    }



    protected function _getButtonUrl()

    {

        if(empty($this->userInfo)) {

            return $this->client->createAuthUrl();

        } else {

            return $this->getUrl('cedsociallogin/yahoo/disconnect');

        }

    }



    protected function _getButtonText()

    {

        if(empty($this->userInfo)) {

            if(!($text = Mage::registry('ced_sociallogin_button_text'))){

                $text = $this->__('Connect');

            }

        } else {

            $text = $this->__('Disconnect');

        }

        

        return $text;

    }


    protected function _getButtonClass()

    {

        if(empty($this->userInfo)) {


                $text = "ced_yahoo_connect";


        } else {

                $text = "ced_yahoo_disconnect";

        }

        

        return $text;

    }



}

