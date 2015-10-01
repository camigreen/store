<div class="ttop ttop-account-edit uk-grid">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center">
			<?php echo $this->title; ?>
			<?php if($this->account->id) : ?>
				<div class="uk-article-lead"><?php echo $this->account->name.' | ID: '.$this->account->id; ?></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="uk-width-1-1 uk-margin">
		<div class="uk-grid menu-buttons">
			<div class="uk-width-1-6">
	    		<button class="uk-button uk-button-success uk-width-1-1 uk-margin-small-bottom" data-task="apply">Save</button>
	    	</div>
	    	<div class="uk-width-1-6">
	    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save">Save and Close</button>
	    	</div>
	    	<div class="uk-width-1-6">
	    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save2new">Save and New</button>
	    	</div>
	    	<div class="uk-width-1-6">
	    		<button class="uk-width-1-1 uk-button uk-button-default uk-margin-small-bottom" data-task="cancel">Cancel</button>
	    	</div>
    	</div>
	</div>
	<div class="uk-width-2-10 side-bar">
		<div class="uk-grid" uk-grid-margin>
			<div class="uk-grid-margin-1-1 uk-text-small">
				<div>Created:</div>
				<div class="uk-text-muted"><?php echo $this->account->created == null ? JText::_('Not created') : $this->app->html->_('date', $this->account->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></div>
				<div>Created By:</div>
				<div class="uk-text-muted"><?php echo $this->account->created_by ? $this->app->user->get($this->account->created_by)->name : null; ?></div>
				<div>Modified:</div>
				<div class="uk-text-muted"><?php echo $this->account->modified == null ? JText::_('Not modified') : $this->app->html->_('date', $this->account->modified, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></div>
				<div>Modified By:</div>
				<div class="uk-text-muted"><?php echo $this->account->modified_by ? $this->app->user->get($this->account->modified_by)->name: null; ?></div>

			</div>
		</div>
	</div>
	<div class="uk-width-8-10">
		<div class="uk-width-1-1">
			<form id="account_admin_form" class="uk-form" method="post" action="<?php echo $this->baseurl; ?>" enctype="multipart/form-data">
				<?php echo $this->partial($this->account->type); ?>
				<input type="hidden" name="task" value="apply" />
				<input type="hidden" name="aid" value="<?php echo $this->account->id; ?>" />
				<input type="hidden" name="type" value="<?php echo $this->account->type; ?>" />
				<?php echo $this->app->html->_('form.token'); ?>

			</form>
			<script>
				jQuery(function($) {

					$(document).ready(function(){
						$('button').on('click', function(e) {
							e.preventDefault();
							var task = $(e.target).data('task');
							if(!task) {
								return;
							}
							var form = document.getElementById('account_admin_form');
							form.task.value = task;
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
</div>