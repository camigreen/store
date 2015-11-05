<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


/*
	Class: ElementSelect
		The select element class
*/
class ElementPrice extends ElementStore {
    
        public function __construct() {
            parent::__construct();
            $this->app->path->register(dirname(__FILE__).'/assets/', 'assets');
            require($this->app->path->path('prices:retail.php'));
            $this->prices = $this->app->parameter->create($prices);
        }

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
                return false;
	}
        
        public function render($params = array())
        {
            $price_params = $params['pricing'];
            $type = $params['type'];
            $prices = $this->app->prices->create($type, $price_params);

            
            if($account = $this->app->account->getCurrent()) {
                $prices['types'] = array('dealer' => $account->elements->get('pricing')['discount']);
                $html[] = '<div>Dealer Price</div>';
                $html[] = '<span id="dealer-price" class="uk-width-1-1"><i class="currency"></i><span class="price" data-discount=".20">0.00</span></span>';
                $html[] = '<div>Retail Price</div>';
            }
            $html[] = '<span id="retail-price" class="uk-width-1-1" ><i class="currency"></i><span class="price" data-price='.json_encode($prices).'>0.00</span></span>';
            $html[] = '<div id="price" data-price='.json_encode($prices).'></div>';
            return implode("\n", $html);

        }
        
        public function hasValue($params = array())
        {
            return true;
        }

        public function loadAssets() {
            parent::loadAssets();
            $this->app->document->addScript('elements:cart/assets/js/shop.js');
        }
}