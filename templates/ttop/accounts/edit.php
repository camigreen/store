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
			    		
			    	</div>
			    	<div class="uk-width-1-6">
			    		
			    	</div>
			    	<div class="uk-width-1-6">
			    		
			    	</div>
			    	<div class="uk-width-1-6">
			    		

			    	</div>
		    	</div>
			</div>
			<div class="uk-width-1-6 side-bar">
				<div class="uk-grid" uk-grid-margin>
					<div class="uk-width-1-1 menu-buttons">
						<button class="uk-button uk-button-success uk-width-1-1 uk-margin-small-bottom" data-task="apply">Save</button>
						<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save">Save and Close</button>
						<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save2new">Save and New</button>
						<button class="uk-width-1-1 uk-button uk-button-default uk-margin-small-bottom" data-task="cancel">Cancel</button>
					</div>
				</div>
				
			</div>
			<div class="uk-width-5-6">
				<div class="uk-width-1-1">
					<form id="account_admin_form" class="uk-form" method="post" action="<?php echo $this->baseurl; ?>" enctype="multipart/form-data">
						<?php foreach($this->groups as $group => $count) : ?>
						<div class="uk-form-row">
							<?php 
								if($group == 'core') {
									echo $this->paramform->setValues($this->account)->render('core', $group); 
								} else {
									echo $this->paramform->setValues($this->account->params->get($group.'.'))->render('params['.$group.']', $group);
								}
							?>
						</div>
						<?php endforeach; ?>
						<input type="hidden" name="account_type" value="<?php echo $this->account->type; ?>" />
						<input type="hidden" name="task" value="apply" />
						<input type="hidden" name="aid" value="<?php echo $this->account->id; ?>" />
					</form>
					<script>
						jQuery(function($) {

							$(document).ready(function(){
								$('.menu-buttons button').on('click', function(e) {
									var task = $(e.target).data('task');
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