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
        $account = $this->app->account->getCurrent()->getParentAccount();
        $layout = $account->type;
        $item_id = $params['id'];
        $group = $pricing->get('group').$pricing->get('option_values');
        if(file_exists($this->app->path->path('elements:price/tmpl/'.$layout.'.php')) && $layout != 'default') {
            return $this->renderLayout($this->app->path->path('elements:price/tmpl/'.$layout.'.php'), compact('group','params', 'item_id'));
        } else {
            return $this->renderLayout($this->app->path->path('elements:price/tmpl/default.php'), compact('prices','params'));
        }
        

    }
    
    public function hasValue($params = array())
    {
        return true;
    }

}