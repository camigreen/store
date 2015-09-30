<?php
	$multiple = $node->attributes()->multiple == 1 ? 'multiple' : null;
	$name = $control_name.'['.$name.']';
	$name .= $multiple ? '[]' : '';
	$html[] = '<select name="'.$name.'" class="'.$class.'" '.$multiple.'>';
	if(!$multiple) {
		$html[] = '<option value="0">- Select -</option>';
	}

	
	$accounts = explode(',', (string) $node->attributes()->account_type);
	$accounts = $this->app->account->getByTypes($accounts);
	$value = (array) $value;
	foreach($accounts as $key => $account) {
		$html[] = '<option value="'.$key.'" '.(in_array($key, $value) ? "selected" : "").' >'.$account->name.'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>