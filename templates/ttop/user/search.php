<?php

?>
<div class="ttop ttop-account-search">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center">
			<?php echo $this->title; ?>
		</div>
	</div>
	<form id="user_admin_form" method="post" action="<?php echo $this->baseurl; ?>">
	<div class="uk-width-1-1 uk-margin-bottom">	
		<button class="uk-button uk-button-success" data-task="add"><span class="uk-icon uk-icon-plus-circle"></span>New User</button>
	</div>

	<div class="uk-width-1-1">
		
		<table class="uk-table uk-table-condensed uk-table-striped uk-table-hover order-table">
			<thead>
				<tr>
					<th></th>
					<th class="uk-width-2-10">Name</th>
					<th class="uk-width-3-10">E-Mail</th>
					<th class="uk-width-1-10">Account</th>
					<th class="uk-width-1-10">Type</th>
					<th class="uk-width-1-10">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->users) < 1) : ?>
					<tr><td colspan="7" class="uk-text-center">No Users Found!<?php echo count($this->users); ?></td></tr>
				<?php endif; ?>
				<?php foreach($this->users as $user) : ?>
				<tr>
					<td class="uk-text-center" >
						<button data-task="edit" data-id="<?php echo $user->id; ?>" class="uk-button" >Edit</button>
						<button data-task="delete" data-id="<?php echo $user->id; ?>" class="uk-button" >Delete</button>
					</td>
					<td><?php echo $user->name; ?></td>
					<td><?php echo $user->email; ?></td>
					<td><?php echo $this->app->suser->getAccountName($user); ?></td>
					<td><?php echo Jtext::_('USER_'.$user->type); ?></td>
					<td><?php echo $this->app->suser->getStatus($user); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="uid" value="0" />
	</div>
	</form>
		<script>
			jQuery(function($) {

				$(document).ready(function(){

					$('button').on('click', function(e) {
						var button = $(e.target);
						$('[name="task"]').val(button.data('task'));
						$('[name="uid"]').val(button.data('id'));
						$('form#user_admin_form').submit();

					})
				})
			})
		</script>
	
</div>