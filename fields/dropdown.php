<?php 
$disabled = (bool) $node->attributes()->disabled ? 'disabled' : '';
 $html[] = '<select id="'.uniqid('select-'.$name.'-').'" class="'.$class.'" '.$disabled.'>';

 foreach($node->option as $option) {
 	$html[] = '<option value="'.$option->attributes()->value.'">'.$option.'</option>';
 }
 $html[] = '</select>';

echo implode("\n", $html);
?>