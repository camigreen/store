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


        $pricing = $params['pricing'];
        $account = $this->app->account->getCurrent();
        $layout = $account->type;
        $display = $account->elements->get('pricing.display', 'retail');
        $pricing_str = $pricing->get('group').$pricing->get('option_values');
        $prices['discount'] = $this->app->prices->get($pricing_str, 'discount');
        $prices['retail'] = $this->app->prices->get($pricing_str, 'retail');
        $prices['markup'] = $this->app->prices->get($pricing_str, 'markup');
        if(file_exists($this->app->path->path('elements:price/tmpl/'.$layout.'.php')) && $layout != 'default') {
            return $this->renderLayout($this->app->path->path('elements:price/tmpl/'.$layout.'.php'), compact('prices','params', 'display'));
        } else {
            return $this->renderLayout($this->app->path->path('elements:price/tmpl/default.php'), compact('prices','params'));
        }
        

    }
    
    public function hasValue($params = array())
    {
        return true;
    }

}