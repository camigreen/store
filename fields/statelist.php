<?php

	$name = $control_name.'['.$name.']';
	$html[] = '<select name="'.$name.'" class="'.$class.'">';

	$states = $this->app->status->getList($node->attributes()->value);
	foreach($states as $key => $state) {
		$html[] = '<option value="'.$key.'" '.($value == $key ? "selected" : "").' >'.JText::_($state).'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>
	

