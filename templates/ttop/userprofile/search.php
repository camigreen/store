<?php

?>
<div class="ttop ttop-account-search">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center">
			<?php echo $this->title; ?>
		</div>
	</div>
	<div class="uk-width-1-1 uk-margin-bottom">	
		<button class="uk-button uk-button-success" data-task="edit"><span class="uk-icon uk-icon-plus-circle"></span>New User</button>
	</div>

	<div class="uk-width-1-1">
		<table class="uk-table uk-table-condensed uk-table-striped uk-table-hover order-table">
			<thead>
				<tr>
					<th class="uk-width-1-10"></th>
					<th class="uk-text-center uk-width-1-10" >ID</th>
					<th class="uk-width-2-10">Name</th>
					<th class="uk-width-2-10">E-Mail</th>
					<th class="uk-width-2-10">Account</th>
					<th class="uk-width-1-10">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php if($this->noRecords) : ?>
					<tr><td colspan="7" class="uk-text-center">No Users Found!</td></tr>
				<?php endif; ?>
				<?php foreach($this->users as $user) : ?>
				<?php 
					$account = $user->getAccount(); 
					$_user = $user->getUser();
				?>
				<tr>
					<td class="uk-text-center" ><button data-task="edit" data-id="<?php echo $user->id; ?>" class="uk-button uk-button-primary" >Edit</button></td>
					<td><?php echo $user->id; ?></td>
					<td><?php echo $_user->name; ?></td>
					<td><?php echo $_user->email; ?></td>
					<td><?php echo $account ? $account->name : null; ?></td>
					<td><?php echo $user->getState(); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<form id="user_admin_form" method="post" action="<?php echo $this->baseurl; ?>">
			<input type="hidden" name="task" value="edit" />
			<input type="hidden" name="uid" value="0" />
		</form>
		<script>
			jQuery(function($) {

				$(document).ready(function(){

					$('button').on('click', function(e) {
						var task = $(e.target).data('task');
						console.log(task);
						var form = document.getElementById('user_admin_form');
						form.task.value = task;
						form.uid.value = $(e.target).data('id');
						var button = document.createElement('input');
						button.style.display = 'none';
						button.type = 'submit';

						form.appendChild(button).click();

						form.removeChild(button);
					})
				})
			})
		</script>
	</div>
</div>