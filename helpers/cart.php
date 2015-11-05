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
class CartHelper extends AppHelper {
    
    protected $_items;
    
    public function __construct($app) {
        parent::__construct($app);
        $this->_items = array();
        //$this->app->loader->register('CartItem','classes:cartitem.php');

    }

    public function create() {

        $this->_items = array();

        $items = $this->app->parameter->create($this->app->session->get('cart',array(),'checkout'));

        $this->add($items);


        return $this;

    }

    public function get($key) {
        return $this->_items[$key]; 
    }

    public function getAllItems() {
        return $this->_items;
    }

    public function add($items) {

    	foreach($items as $key => $item) {

    		$_item = new CartItem($this->app, $item);
            $sku = $_item->sku;
            if (isset($this->_items[$sku])) {
                $this->_items[$sku]->qty += $_item->qty;
            } else {
                $this->_items[$sku] = $_item;
            }
    	}
        return $this->updateSession();
    }

    public function remove($sku) {
        if (isset($this->_items[$sku])) {
            unset($this->_items[$sku]);
            return $this->updateSession();
        }
        
        return $this;
    }

    public function getItemCount() {
        $count = 0;
        foreach($this->_items as $item) {
            $count += (int) $item->qty;
        }
        return $count;
    }

    public function getCartTotal() {
        $total = 0.00;
        foreach($this->_items as $item) {
            
            $total += $item->getTotal();
        }
        return $total;
    }

    public function emptyCart() {
        $this->_items = array();
        return $this->updateSession();
    }

    public function updateQuantity($sku, $qty) {

        if ($qty == 0) {
            return $this->remove($sku);
        } else {
            $this->_items[$sku]->qty = $qty;
        }
        
        return $this->updateSession();
    }

    public function updateSession() {
        $data = $this->app->data->create($this->_items);
        $this->app->session->set('cart',(string) $data,'checkout');
        return $this;
    }
    
}

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class CartItem {
    
    public $id;
    
    public $name;
    
    public $qty;
    
    public $price;
    
    public $total = 0;
    
    public $shipping;
    
    public $options;
    
    public $attributes;
    
    public $description;
    
    public $make;
    
    public $model;

    public $discount = 0;
    
    public $pricepoints = array();
    
    public $sku;
    
    public $taxable = true;
    
    public $app;
    
    public function __construct($app, $item) {
        
        foreach ($item as $key => $value) {
            $this->$key = $value;
        }
        $this->discount = (float) $this->discount;
        $this->app = $app;
        $this->options = $app->parameter->create($this->options);
        $this->attributes = $app->parameter->create($this->attributes);
        $this->shipping = $app->parameter->create($this->shipping);
        //var_dump($this->options);
        $this->generateSKU();
        $this->getTotal();
        
    }

    
    public function getDescription($type) {
        $lines = array();
        if ($type == 'receipt') {
            foreach($this->options as $option) {
                if (!isset($option['visible']) || $option['visible']) {
                    $lines[] = $option['name'].': '.$option['text'];
                }
            } 
            
        } else {
            foreach($this->options as $option) {
                $lines[] = $option['name'].': '.$option['text'];
            } 
        }
        $html = isset($lines) ? '<p>'.implode('</p><p>', $lines).'</p>' : '';
        return $html;
    }
    public function generateSKU() {
        $options = '';
        foreach($this->options as $key => $value) {
            $options .= $key.$value['text'];
        }
        
        $this->sku = hash('md5', $this->id.$options);
        return $this->sku;
    }
    
    public function getTotal() {
        $discount = $this->price*$this->discount;
        $this->total = ($this->price - $discount)*$this->qty;
        return $this->total;
    }

    public function getOptions() {
        if (count($this->options) > 0) {
            $html[] = "<ul class='uk-list options-list'>";
            foreach($this->options as $option) {
                $html[] = '<li><span class="option-name">'.$option['name'].':</span><span class="option-text">'.$option['text'].'</span></li>';
            }
            $html[] = "</ul>";

            return implode('',$html);
        }
    }

    public function toLog() {
        foreach($this as $key => $value) {
            if($key != 'app') {
                $string[] = $key.': '.$value;
            }
        }
        return implode(PHP_EOL,$string).PHP_EOL.'/////// End of Log ////////'.PHP_EOL;
    }
 
}
