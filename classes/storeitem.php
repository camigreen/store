<?php defined('_JEXEC') or die('Restricted access');

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class StoreItem {
    
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
        $this->params = $this->app->parameter->create();
        $this->generateSKU();
        $this->getTotal('discount');
    }


    public function generateSKU() {
        $options = '';
        foreach($this->options as $key => $value) {
            $options .= $key.$value->get('text');
        }
        
        $this->sku = hash('md5', $this->id.$options);
        return $this->sku;
    }

    public function getPrice($type = 'retail') {
        return (float) $this->app->prices->get($this->pricing, $type);
    }

    public function isProcessed() {
        return $this->params->get('processed', false);
    }
    
    public function getTotal($type = 'retail', $formatCurrency = false, $currency = 'USD') {
        if($this->isProcessed()) {
            return $this->total;
        }
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