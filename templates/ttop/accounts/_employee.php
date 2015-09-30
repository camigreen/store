<?php $this->form->setValues($this->account->getUser()); ?>
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
				if($this->account->isCurrentUser()) {
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
<?php if($this->form->checkGroup('elements')) : ?>
	<div class="uk-form-row">
		<fieldset id="elements">
			<legend>User Assignments</legend>
			<?php echo $this->form->render('elements')?>
		</fieldset>
	</div>
<?php endif; ?>

<input type="hidden" name="elements[user]" value="<?php echo $this->account->elements->get('user'); ?>" />