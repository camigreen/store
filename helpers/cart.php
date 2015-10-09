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

        //$this->app->loader->register('CartItem','classes:cartitem.php');

    }

    public function create() {

        $this->_items = $this->app->parameter->create();

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

        if(!$this->_items) {
            $this->_items = $this->app->parameter->create();
        }

    	foreach($items as $key => $item) {
            var_dump($item);
    		$_item = new CartItem($this->app, $item);
            if ($this->_items->has($_item->getSKU())) {
                $this->_items->get($_item->getSKU())->qty += $_item->qty;
            } else {
                $this->_items->set($_item->getSKU(), $_item);
            }
    	}
        return $this->updateSession();
    }

    public function remove($sku) {
        if ($this->_items->has($sku)) {
            $this->_items->remove($sku);
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
            $total += $item->price*$item->qty;
        }
        return $total;
    }

    public function emptyCart() {
        $this->_items = $this->app->parameter->create();
        return $this->updateSession();
    }

    public function updateQuantity($sku, $qty) {

        if ($qty == 0) {
            return $this->remove($sku);
        } else {
            $this->_items->get($sku)->qty = $qty;
        }
        
        return $this->updateSession();
    }

    public function updateSession() {
        $this->app->session->set('cart',(string) $this->_items,'checkout');
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
    
    public $attributes = array();
    
    public $description;
    
    public $make;
    
    public $model;
    
    public $pricepoints = array();
    
    public $sku;
    
    public $taxable = true;
    
    public $app;
    
    public function __construct($item, $app) {
        $this->app = $app;
        $item->options = $app->data->create($item->options);
        $item->shipping = $app->data->create($item->shipping);
        foreach ($item as $key => $value) {
            $this->$key = $value;
            
        }
        $this->generateSKU();
        
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
    
    public function get($resource, $formatCurrency = false) {
        
        return ($formatCurrency ? $this->formatCurrency(parent::get($resource)) : parent::get($resource));
    }
 
}
