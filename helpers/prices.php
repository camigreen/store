<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class PricesHelper extends AppHelper {
    
    
    public function create($type, $params = array()) {
    	include $this->app->path->path('prices:retail.php');
        $item = $prices[$type]['item'];
        $shipping = $prices[$type]['shipping'];
        $result = array();
        foreach($params as $param) {
            if(isset($item[$param])) {
                $result['item'] = $item[$param];
            }
            if(isset($shipping[$param])) {
                $result['shipping'] = $shipping[$param];
            }
        }
        return $this->app->data->create($result);
    }
}

class StoreAppException extends AppException {}

