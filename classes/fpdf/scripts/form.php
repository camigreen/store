<?php

class FormPDF extends GridPDF {
	public $form;
	public $app;
	public $_pages;
	public $type;
	public $table_x = 0;

	public function __construct($app, $type) {
		$this->app = $app;
		$this->type = $type;
		$path = $this->app->path->path('classes:fpdf/scripts/'.$type.'.xml');
	    $this->form = simplexml_load_file($path);
	    $this->grid = (bool) (string) $this->form->grid;
	    $this->loadPages();
    	parent::__construct();
	}

	public function generate() {
		$margins = $this->form->margins;
		$this->SetMargins((int)$margins->left,(int)$margins->top, (int) $margins->right);
		
	    $this->_AddPage(1,'P','Letter');
	    $this->arrangeItems();
	    $this->_AddPage(1,'P','Letter');
	    
	    // foreach($this->items as $item) {
	    // 	$data[] = array(
	    // 		'name' => $item->name,
	    // 		'qty' => $item->qty,
	    // 		'price' => $item->price
	    // 	);
	    // }
	    // foreach($data as $item) {
	    // 	$this->populateTable($item);
	    // }
	    
	    return $this;
	        
	}

	public function _AddPage($page, $orientation='P', $size='letter') {

		$this->AddPage($orientation, $size);
		$this->SetAutoPageBreak(false);
	    $this->formTitle();
	    foreach($this->_pages->$page->fields as $field) {
	    	if($field->type != 'table') {
	    		$this->{$field->type}($field);
	    	}
	    }
	    $this->populate($this->order_data);
	}

	public function setData($order) {
    $billing = $order->billing;
    $shipping = $order->shipping;
    $data['billto'] = array(
                $billing->firstname.' '.$billing->lastname,
                $billing->address,
                $billing->city.', '.$billing->state.'  '.$billing->zip,
                $billing->phoneNumber,
                $billing->altNumber,
                $billing->email
            );
    if($order->localPickup == false) {
        $data['shipto'] = array(
            $shipping->firstname.' '.$shipping->lastname,
            $shipping->address,
            $shipping->city.', '.$shipping->state.'  '.$shipping->zip,
            $shipping->phoneNumber,
            $shipping->altNumber
        );
    }
    $data['order_date'] = $order->getOrderDate();
    $data['salesperson'] = $order->getSalesPerson();
    $data['order_number'] = $order->id;
    $data['delivery_method'] = $order->localPickup ? 'Local Pickup' : 'UPS Ground';
    $data['payment_information'] = $order->creditCard->card_name.' ending in '.substr($order->creditCard->cardNumber, -4);
    $data['subtotal'] = '$'.number_format($order->subtotal,2,'.','');
    $data['shipping'] = '$'.number_format($order->ship_total,2,'.','');
    $data['taxes'] = '$'.number_format($order->tax_total,2,'.','');
    $data['total'] = '$'.number_format($order->total,2,'.','');
    $this->order_data = $data;
    $this->items = $this->app->data->create($order->items);
    return $this;
	}

	// Company
	function company($field) {
		$x = $field->params->get('x');
		$y = $field->params->get('y');
		$w = $field->params->get('w');
		$h = $field->params->get('h');
		$title = $field->params->get('title');
		$border = $field->params->get('border');

	    $company = $this->form->data->company;
	    $name = $company->name;
	    $address = array(
	        (string) $company->address->street,
	        (string) $company->address->city.', '.(string) $company->address->state.'  '.(string) $company->address->zip,
	        (string) $company->phone,
	        (string) $company->website,
	        (string) $company->email
	    );
	    $this->SetXY($x, $y);
	    $this->SetFont('Arial','B',12);
	    $this->Cell( $w, 7, $name, $border, 1);
	    $this->SetFont('Arial','',10);
	    foreach($address as $line) {
	    	$this->SetX($x);
	    	$this->Cell( $w, 4, $line, $border, 1);
	    }
	}

	public function box($field) {

		$params = $field->params;
		$this->SetXY($params->x, $params->y);

		$this->Cell($params->w, $params->h, '', 1);

		if ($title = $field->get('title')) {
			$this->SetXY($params->x, $params->y-4);
			$this->SetFont('Arial','B',8);
			$this->Cell($params->w, 4, $title, 'R,L,T',0,'C');
		}


	}
	public function textbox($field) {
		$params = $field->params;
		$this->SetXY($params->x, $params->y);
		$this->Cell($params->w, $params->h, '', $params->get('border'), 0);
		if ($title = $field->get('title')) {
			if(is_object($title)) {
				$title_params = $title->get('params');
				$align = $title_params->get('align','L');
				$this->SetFont('Arial','B',8);
				switch($align) {
					case 'R':
						$this->SetXY(($params->x+$params->w), $params->y);
						break;
					case 'L':
						$this->SetXY($params->x-$title_params->get('w',20), $params->y);
						break;
					case 'T':
						$this->SetXY($params->x,$params->y-$title_params->get('h',5));
						break;
					case 'B':
						$this->SetXY($params->x,$params->y+$title_params->get('h',5));
						break;
					default:
						$this->SetXY($params->x-$title_params->get('w',20), $params->y);
				}
				$this->Cell($title_params->get('w',20), $title_params->get('h',$params->h), $title->get('text',''), $title_params->get('border',0),0, $title_params->get('text-align','L'));
			}
			// 
			// 
			// 
		}
	}

	public function logo($field) {
		$x = $field->params->get('x');
		$y = $field->params->get('y');
		$w = $field->params->get('w');
		$h = $field->params->get('h');
		$border = $field->params->get('border');
		$path = (string) $this->form->data->company->logoPath;
		$this->Image($path,$x,$y,$w,$h);
	}

	public function formTitle() {
		$this->SetXY(145,8);
		$this->SetFont( "Arial", "B", 30);
		$text  = strtoupper((string) $this->form->name);
		$this->Cell(60,10, $text, 0, 0, "R" );
    
	}

	public function toFile() {
	    $name = $this->app->utility->generateUUID().'.pdf';
	    $path = $this->app->path->path('assets:pdfs/');
	    $this->Output($path.$name,'F');
	    return $name;
	}

	public function toBrowser() {
	    return $this->Output($this->type.'.pdf',"I");
	}

	public function xml2obj($xml) {
		$arr = array();

	    foreach ($xml as $element)
	    {
	    	$attrs = $element->attributes();
	        $tag = $element->getName();
	        $e = get_object_vars($element);
	        if (!empty($e))
	        {
	        	$test = $element instanceof SimpleXMLElement ? $this->xml2obj($element) : $e;
	        	if(!isset($test->name) || is_object($test->name)) {
	        		$name = $tag;
	        		if (!trim($element) == '') {
						$test->text = trim($element);
	        		}
	        		
	        	} else {
	        		$name = $test->name;
	        	}
	        	if($attrs) {
		        	foreach($attrs as $key =>$attr) {
		        		$params[$key] = (string) $attr;
		        	}
		        	$test->params = $this->app->data->create($params);
	        	}
	        	$arr[$name] = $test;
	        }
	        else
	        {
	            $arr[$tag] = trim($element);
	        }
	    }
	    return $this->app->data->create($arr);
	}

	public function loadPages() {

		$pages = $this->form->pages->page;
		
			$obj = $this->xml2obj($pages);
			$this->_pages = $obj;
	}

	public function populate($data) {
		$this->SetFont('Arial','',8);
		$fields = $this->_pages->fields;
		foreach($data as $key => $value) {
			if(isset($fields->$key)) {
				$field = $fields->$key;
				if(is_string($value)) {
					$this->SetXY($field->params->x, $field->params->y);
					$this->Cell($field->params->w, $field->params->h, $value, 0, 0, $field->params->get('align'));
				} else {
					$this->SetXY($field->params->x+$field->params->get('data-padding'), $field->params->y);
					$txt = implode("\n",$value);
					$this->Multicell($field->params->w,5, $txt);
				}
			}
			
		}

		//var_dump($data);
	}

	public function arrangeItems() {

		$columns = array('name','qty','price');
		foreach($this->items as $item) {
	    	$data[] = array(
	    		'name' => $item->name,
	    		'qty' => $item->qty,
	    		'price' => $item->price,
	    		'options' => implode("\n",$item->options)
	    	);
	    }
	    $i = 0;
	    foreach($data as $row) {
	    	echo '<pre>';
			var_dump($this->NbLines(100,$row['options']));
			echo '</pre>';
	    }
		

	}

	public function populateTable($data) {
		$field = $this->_fields['name'];
		$hgt = 0;
		foreach($data as $key => $value) {
			if(isset($this->_fields[$key])) {
				$field = $this->_fields[$key];
				$this->SetFont('Arial','',$field->params->get('fontsize',8));
				if(!is_array($value)) {
					if($field->params->get('format') == 'currency') {
						$value = $this->app->number->currency($value,array('currency' => 'USD'));
					}
					$this->SetXY($field->params->x, $field->params->y + $this->table_x);
					$this->Cell($field->params->w, 5, $value, 0, 0, $field->params->get('align'));
					
				} else {
					$this->SetXY($field->params->x+$field->params->get('data-padding'), $field->params->y);
					$txt = implode("\n",$value);
					$this->Multicell($field->params->w,$this->table_LH, $txt);
				}
			}
			
		}
		$hgt += 5;
		$this->table_x += $hgt;
	}

	function NbLines($w,$txt) {
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $i=0;
	    $ll=0;
	    $wl=0;
	    $arr = array();
	    $text = '';
	    $word = '';
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        // Check if the character is a newline
	        if($c=="\n")
	        {
	            $i++;
	            $ll=0;
	            $wl=0;
	            $text .= $word;
	            $arr[] = $text;
	            $text = '';
	            $word = '';
	            continue;
	        }
	        // Check if the character is a space
	        if($c==' ') {
	        	if (($ll + $wl) > $wmax) { // if the line length + word length is greater than the length allowed.
	        		$arr[] = trim($text);
	        		$word .= $c;
	        		$wl += $cw[$c];
	        		$text = $word;
	        		$ll = $wl;
	        		$word = '';
	        		$wl=0;
	        		$i++;

	        	} else {
	        		$word .= $c;
	        		$wl += $cw[$c];
	        		$text .= $word;
	        		$ll += $wl;
	        		$word = '';
	        		$wl=0;
	        		$i++;
	        	}
	        	continue;
	        }
	        $word .= $c;
	        $wl += $cw[$c];
	        $i++;

	    }
	    if(($ll + $wl) > $wmax) {
	    	$arr[] = trim($text);
	    	$arr[] = trim($word);
	    } else {
	    	$text .= $word;
	    	$arr[] = $text;
	    }
	    return $arr;
	}
}

class FormTablePDF {

	public $columns;
	public $height;
	public $width;
	public $x;
	public $y;
	public $header;

	public function addColumns($data = array()) {
		foreach($data as $column) {
			$this->column[$column['header']] = array(
				'w' => $column['w']
			);
		}
	}



}

?>