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

        $priceOptions = $params['priceOptions'];
        $price = $this->app->prices->getRetail($priceOptions->get('group'), $priceOptions->get('options.'));
        $price = number_format($price, 2);
    
        return '<div id="'.$params['id'].'-price"><i class="currency"></i><span class="price">'.$price.'</span></div>';

    }
    
    public function hasValue($params = array())
    {
        return true;
    }

}