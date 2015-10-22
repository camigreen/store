<?php
	$multiple = $node->attributes()->multiple == 1 ? 'multiple' : null;
	$name = $control_name.'['.$name.']';
	$name .= $multiple ? '[]' : '';
	$html[] = '<select name="'.$name.'" class="'.$class.'" '.$multiple.'>';
	if(!$multiple) {
		$html[] = '<option value="0">- Select -</option>';
	}

	
	$types = explode(',', (string) $node->attributes()->account_type);
	$conditions = array();
	foreach($types as $type) {
		$conditions[] = empty($conditions) ? 'type = "'.$type.'"' : ' OR type = "'.$type.'"';
	}
	$condition = implode("\n",$conditions);

	$accounts = $this->app->table->account->all(array('conditions' => $condition));
	$value = (array) $value;
	foreach($accounts as $key => $account) {
		$html[] = '<option value="'.$key.'" '.(in_array($key, $value) ? "selected" : "").' >'.$account->name.'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>