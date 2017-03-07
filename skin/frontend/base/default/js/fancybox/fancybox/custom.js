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
jQuery(document).ready(function()
		            {

						jQuery("a.fancybox").fancybox
						({
						        'width'           : 600,
						        'height'              : 650,
						        'overlayOpacity'     :  '0.4',
						        'overlayColor'       :  '#000',
						        'hideOnContentClick' :   false,
						        'autoScale'          :   false,
					            'transitionIn'       :   'elastic',
					            'transitionOut'  :   'elastic',
					            'type'           :   'iframe'
					    });
						jQuery(".various").fancybox
						    ({
								 width         : '650',
							     height        : '340',
							     'transitionIn'  : 'none',
							     'transitionOut' : 'none',
							     'autoDimensions': false,
							     'autoScale'     : false,
							});
                   });

					jQuery.noConflict();

					jQuery(function () {
					    //  Accordion Panels
						
					    jQuery(".accordion li").click(function () {
					    	jQuery(this).next(".pane").slideToggle("slow").siblings(".pane:visible").slideUp("slow");
					    	jQuery(this).toggleClass("current");
					    	jQuery(this).siblings("li").removeClass("current");
					    });
					});
						
