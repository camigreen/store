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

    public function getCartTotal($display = 'markup') {
        $total = 0.00;
        foreach($this->_items as $item) {
            
            $total += $item->getTotal($display);
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
    
    public $total = 0;
    
    public $shipping;
    
    public $options;
    
    public $attributes;
    
    public $description;
    
    public $make;
    
    public $model;
    
    public $pricing;
    
    public $sku;
    
    public $taxable = true;
    
    public $app;

    protected $price;
    
    public function __construct($app, $item) {

        
        foreach ($item as $key => $value) {
            $this->$key = $value;
        }
        
        $this->app = $app;
        $options = $this->options;
        $this->options = $this->app->parameter->create();
        foreach($options as $key => $option) {
            $opt = $this->app->parameter->create($option);
            $this->options->set($key, $opt);
        }
        $this->attributes = $app->parameter->create($this->attributes);
        $this->shipping = $app->parameter->create($this->shipping);
        $this->pricing = $app->parameter->create($this->pricing);
        $this->price = $this->app->parameter->create();
        $account = $this->app->customer->getAccount();
        $markup = $account->params->get('pricing.markup');
        $discount = $account->params->get('pricing.discount');
        $this->price->set('retail', $this->app->prices->get($this->pricing->get('group'), 0));
        $this->price->set('markup', $this->pricing->get('markup', $markup));
        $this->price->set('discount', $discount); 
        //var_dump($this->options);
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
        if($this->sku) {
            return $this->sku;
        }
        $options = '';
        foreach($this->options as $key => $value) {
            $options .= $key.$value->get('text');
        }
        $options .= $this->getPrice();
        
        $this->sku = hash('md5', $this->id.$options);
        return $this->sku;
    }

    public function getPrice($type = 'markup') {
        $price = $this->price->get('retail', 0);

        switch($type) {
            case 'markup':
                $price += $price*$this->price->get('markup');
                break;
            case 'discount':
                $price -= $price*$this->price->get('discount');
                break;
        }

        return (float) $price;
    }
    
    public function getTotal($type = 'markup', $formatCurrency = false, $currency = 'USD') {
        $price = $this->getPrice($type);
        $this->total = $price*$this->qty;
        if($formatCurrency) {
            return $this->app->number->currency($this->total, array('currency' => $currency));
        }
        return $this->total;
    }

    public function getOptions() {
        if (count($this->options) > 0) {
            $html[] = "<ul class='uk-list options-list'>";
            foreach($this->options as $option) {
                $html[] = '<li><span class="option-name">'.$option->get('name').':</span><span class="option-text">'.$option->get('text').'</span></li>';
            }
            $html[] = "</ul>";

            return implode('',$html);
        }
    }

    public function export() {
        $result = $this->app->parameter->create();
        foreach($this as $key => $value) {
            if(is_array($value)) {
                $result->set($key.'.', $value);
            } else {
                $result->set($key, $value);
            }
        }
        return $result;
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
