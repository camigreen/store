<?php 
	$value = (array) $value;
	$users = array();
	foreach($value as $id) {
		$users[] = $this->app->account->get($id, 'user');
	}
?>
<div class="uk-width-1-1">
<?php if(!empty($users)) : ?>
	<ul class="uk-list uk-list-striped">
	<?php foreach($users as $user) : ?>
		<?php echo '<li id="'.$user->id.'">'.$user->name.'<a href="#" class="uk-close uk-float-right uk-text-muted"></a></li>'; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php if(empty($users)) : ?>
	<p class="uk-text-small">There are no users assigned to this account.</p>
<?php endif; ?>
</div>

<!-- This is a button toggling the modal -->
<button class="uk-button" data-uk-modal="{target:'#user-modal'}">Add User</button>

<!-- This is the modal -->
<div id="user-modal" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        Add User Modal
    </div>
</div>