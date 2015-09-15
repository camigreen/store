<?php

?>
<div class="">
	<div class="uk-article-title uk-text-center">
		<?php echo $this->title; ?>
	</div>
</div>

<div class="uk-width-1-1">
	<table class="uk-table uk-table-condensed uk-table-striped uk-table-hover order-table">
		<thead>
			<tr>
				<th class="uk-width-1-10"></th>
				<th class="uk-text-center uk-width-1-10" >ID</th>
				<th class="uk-width-2-10">Name</th>
				<th class="uk-width-1-10">Account Number</th>
				<th class="uk-width-2-10">Type</th>
				<th class="uk-width-2-10">Status</th>
			</tr>
		</thead>
		<tbody>
			<?php if($this->record_count <= 0) : ?>
				<tr><td colspan="7" class="uk-text-center">No Orders Found!</td></tr>
			<?php endif; ?>
			<?php foreach($this->accounts as $account) : ?>
			<tr>
				<td class="uk-text-center" ><a class="uk-button uk-button-primary" href="?option=com_zoo&controller=account&task=edit&aid=<?php echo $account->id; ?>">Edit</a></td>
				<td><?php echo $account->id; ?></td>
				<td><?php echo $account->name; ?></td>
				<td><?php echo $account->number; ?></td>
				<td><?php echo $account->getType(); ?></td>
				<td><?php echo $account->getState(); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>