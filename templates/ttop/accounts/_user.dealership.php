<div class="ttop ttop-account-edit uk-grid">
			<div class="uk-width-8-10">
				<div class="uk-width-1-1">
					<form id="account_admin_form" class="uk-form" method="post" action="<?php echo $this->baseurl; ?>" enctype="multipart/form-data">
							<?php $this->form->setValues($this->account); ?>
							<?php $this->form->setValue('status',$this->account->status); ?>
							<?php if($this->form->checkGroup('details')) : ?>
								<div class="uk-form-row">
									<fieldset id="details">
										<legend>Details</legend>
										<?php echo $this->form->render('details')?>
									</fieldset>
								</div>
							<?php endif; ?>
							<?php if($this->form->checkGroup('password')) : ?>
								<div class="uk-form-row">
									<fieldset id="password">
										<legend>Password</legend>
										<?php 
											if($this->app->session->get('user')->id == $this->user->id || !$this->user->id) {
												echo $this->form->render('password');
											} else {
												echo '<button id="resetPWD" class="uk-width-1-3 uk-button uk-button-primary uk-margin" data-task="resetPassword">Reset Password</button>';
											}
										?>
									</fieldset>
								</div>
							<?php endif; ?>
							<?php $this->form->setValues($this->account->elements); ?>
							<?php if($this->form->checkGroup('contact')) : ?>
								<div class="uk-form-row">
									<fieldset id="contact">
										<legend>Contact Info</legend>
										<?php echo $this->form->render('contact')?>
									</fieldset>
								</div>
							<?php endif; ?>
							<?php $this->form->setValue('account', $this->account->getParents()); ?>
							<?php if($this->form->checkGroup('elements')) : ?>
								<div class="uk-form-row">
									<fieldset id="elements">
										<legend>User Assignments</legend>
										<?php echo $this->form->render('elements')?>
									</fieldset>
								</div>
							<?php endif; ?>
						<input type="hidden" name="task" value="apply" />
						<input type="hidden" name="uid" value="<?php echo $this->profile->id; ?>" />
						<input type="hidden" name="elements[type]" value="<?php echo $this->profile->type; ?>" />
						<?php echo $this->app->html->_('form.token'); ?>
					</form>
					<script>
						jQuery(function($) {

							$(document).ready(function(){
								$('button').on('click', function(e) {
									e.preventDefault();
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