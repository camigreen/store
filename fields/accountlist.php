<?php

	$name = $control_name.'['.$name.']';
	$html[] = '<select name="'.$name.'" class="'.$class.'">';
	$html[] = '<option value="0">- Select -</option>';

	$accounts = $this->app->account->getByTypes(array('dealer', 'admin'));
	foreach($accounts as $key => $account) {
		$html[] = '<option value="'.$key.'" '.($value == $key ? "selected" : "").' >'.$account->name.'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>