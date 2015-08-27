<?php 

echo '<h3>Account Page</h3>';

echo 'User';
var_dump($this->user);

echo 'Account';
var_dump($this->account);

$tzoffset   = $this->app->date->getOffset();

$created = $this->app->date->create($this->account->created)->format('m/d/Y g:i a');
$modified = $this->app->date->create($this->account->modified)->format('m/d/Y g:i a');

?>

<div class="uk-width-1-1">
	<form class="uk-form" action="/admin/accounts?task=apply&aid=<?php echo $this->account->id; ?>" method="post"
		<input type="text" name="account[name]" value="<?php echo $this->account->name; ?>" />
		<input type="text" name="account[number]" value="<?php echo $this->account->number; ?>" />
		<input type="text" name="account[type]" value="<?php echo $this->account->type; ?>" />
		<input type="date" name="account[created]" value="<?php echo $created; ?>" />
		<input type="text" name="created_by_name" value="<?php echo $this->app->user->get($this->account->created_by)->name; ?>" />
		<input type="hidden" name="account[created_by]" value="<?php echo $this->account->created_by; ?>" />
		<input type="date" name="account[modified]" value="<?php echo $modified; ?>" />
		<input type="text" name="account[modified_by]" value="<?php echo $this->app->user->get($this->account->modified_by)->name; ?>" />
		<input type="hidden" name="modified_by_name" value="<?php echo $this->account->modified_by; ?>" />
		<input type="text" name="account[params][terms]" value="<?php echo $this->account->params->get('terms', ''); ?>" />
		<input type="text" name="account[params][discount]" value="<?php echo $this->account->params->get('discount', ''); ?>" />
		<input type="submit" class="uk-button uk-button-primary" value="Save" />
	</form>
</div>