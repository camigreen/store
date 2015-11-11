<?php

?>
<div class="ttop-account-search">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center">
			<?php echo $this->title; ?>
		</div>
	</div>
	<form id="account_form" method="post" action="<?php echo $this->baseurl; ?>">
		<div class="uk-width-1-1 uk-margin-bottom">	
			<button id="add_new" class="uk-button uk-button-success"><span class="uk-icon uk-icon-plus-circle"></span>New</button>
		</div>

		<div class="uk-width-1-1">
			<table class="uk-table uk-table-condensed uk-table-striped uk-table-hover">
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
						<tr><td colspan="7" class="uk-text-center">No Accounts Found!</td></tr>
					<?php endif; ?>
					<?php foreach($this->accounts as $account) : ?>
					<tr>
						<td class="uk-text-center" >
							<button id="<?php echo $account->id; ?>" data-task="edit" data-type="<?php echo $account->type; ?>" class="uk-button uk-button-mini" >Edit</button>
							<button id="<?php echo $account->id; ?>" data-task="delete" data-type="<?php echo $account->type; ?>" class="uk-button uk-button-mini" >Delete</button>
						</td>
						<td><?php echo $account->id; ?></td>
						<td><?php echo $account->name; ?></td>
						<td><?php echo $account->elements->get('account_number.number'); ?></td>
						<td><?php echo $account->getType(); ?></td>
						<td><?php echo $account->getState(); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
				<input type="hidden" name="task" value="edit" />
				<input type="hidden" name="aid" value="0" />
				<input type="hidden" name="type" />
				<?php echo JHtml::_('form.token'); ?>
		</div>
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
					var button = $(e.target);
					var form = $('#account_form');

					$('[name="task"]').val(button.data('task'));
					$('[name="aid"]').val(button.prop('id'));
					$('[name="type"]').val(button.data('type'));

					form.submit();	
				})
			})
		})
	</script>
</div>