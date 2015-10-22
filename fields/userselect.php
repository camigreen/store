<?php 
	$id = $parent->getValue('id');
	$users = $this->app->suser->all();
	$elements = $parent->getValue('elements');
	$available = array();
	$selected = array();
	foreach($users as $user) {
		if(!$user->elements->get('account')) {
			$available[$user->id] = $user->name;
		}
		if(in_array($user->id, $elements->get('users', array()))) {
			$selected[$user->id] = $user->name; 
		}
	}
	
	var_dump($elements);
	echo 'Available:';
	var_dump($available);
	echo 'Selected:';
	var_dump($selected);
?>
<div class="uk-width-1-1">
<?php if(!empty($selected)) : ?>
	<ul class="uk-list uk-list-striped">
	<?php foreach($selected as $id => $user) : ?>
		<?php echo '<li id="'.$id.'">'.$user.'<a href="#" class="uk-close uk-float-right uk-text-muted"></a></li>'; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php if(empty($users)) : ?>
	<p class="uk-text-small">There are no users assigned to this account.</p>
<?php endif; ?>
</div>

<input type="text" name="elements[users]" value="<?php echo implode(',',$parent->getValue('elements')->get('users')); ?>" />

<!-- This is a button toggling the modal -->
<button class="uk-button" data-uk-modal="{target:'#user-modal'}">Add User</button>

<!-- This is the modal -->
<div id="user-modal" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <p>Select Users to add to the account.</p>
        	<ul class="uk-list">
        	<?php foreach($available as $id => $user) : ?>
        	<li>
	        	<label>
	    			<input type="checkbox">
	    			<?php echo $user; ?>
	    		</label>
    		</li>
    		<?php endforeach; ?>
    		</ul>
    		<button class="uk-button">Add User(s)</button>
    </div>
</div>