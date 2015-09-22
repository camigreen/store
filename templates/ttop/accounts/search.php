<?php

?>
<div class="ttop ttop-account-search">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center">
			<?php echo $this->title; ?>
		</div>
	</div>
	<div class="uk-width-1-1 uk-margin-bottom">	
		<button id="add_new" class="uk-button uk-button-success"><span class="uk-icon uk-icon-plus-circle"></span>New</button>
	</div>

	<div class="uk-width-1-1">
		<table class="uk-table uk-table-condensed uk-table-striped uk-table-hover order-table">
			<thead>
				<tr>
					<th class="uk-width-1-10"></th>
					<th class="uk-text-center uk-width-1-10" >ID</th>
					<th class="uk-width-2-10">Name</th>
					<th class="uk-width-2-10">Account Number</th>
					<th class="uk-width-1-10">Type</th>
					<th class="uk-width-2-10">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php if($this->record_count <= 0) : ?>
					<tr><td colspan="7" class="uk-text-center">No Orders Found!</td></tr>
				<?php endif; ?>
				<?php foreach($this->accounts as $account) : ?>
				<tr>
					<td class="uk-text-center" ><button id="<?php echo $account->id; ?>" class="uk-button uk-button-primary" >Edit</button></td>
					<td><?php echo $account->id; ?></td>
					<td><?php echo $account->name; ?></td>
					<td><?php echo $account->number; ?></td>
					<td><?php echo $account->getType(); ?></td>
					<td><?php echo $account->getState(); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<form id="account_form" method="post" action="<?php echo $this->baseurl; ?>">
			<input type="hidden" name="task" value="edit" />
			<input type="hidden" name="aid" value="0" />
		</form>
		<script>
			jQuery(function($) {

				$(document).ready(function(){
					$('#add_new').on('click', function() {
						var form = document.getElementById('account_form');
						form.task.value = 'add';
						var button = document.createElement('input');
						button.style.display = 'none';
						button.type = 'submit';

						form.appendChild(button).click();

						//form.removeChild(button);
					})
					$('table button').on('click', function(e) {
						var form = document.getElementById('account_form');
						form.task.value = 'edit';
						form.aid.value = $(e.target).prop('id');
						var button = document.createElement('input');
						button.style.display = 'none';
						button.type = 'submit';

						form.appendChild(button).click();

						//form.removeChild(button);
					})
				})
			})
		</script>
	</div>
</div>