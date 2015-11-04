<?php $this->form->setValues($this->account); ?>
<?php if($this->form->checkGroup('details')) : ?>
	<div class="uk-form-row">
		<fieldset id="details">
			<legend>Details</legend>
			<?php echo $this->form->render('details')?>
		</fieldset>
	</div>
<?php endif; ?>
<?php $this->form->setValues($this->account->elements->get('poc')); ?>
<?php if($this->form->checkGroup('poc')) : ?>
	<div class="uk-form-row">
		<fieldset id="poc">
			<legend>Point of Contact</legend>
			<?php echo $this->form->render('poc')?>
		</fieldset>
	</div>
<?php endif; ?>
<?php $this->form->setValues($this->account->elements); ?>
<?php if($this->form->checkGroup('commission')) : ?>
	<div class="uk-form-row">
		<fieldset id="commission">
			<legend>Account Commission</legend>
			<?php echo $this->form->render('commission')?>
		</fieldset>
	</div>
<?php endif; ?>
<?php if($this->form->checkGroup('elements')) : ?>
	<div class="uk-form-row">
		<fieldset id="elements">
			<legend>Account Elements</legend>
			<?php echo $this->form->render('elements')?>
		</fieldset>
	</div>
<?php endif; ?>
<?php $this->form->setValues($this->account); ?>
<?php if($this->form->checkGroup('parents')) : ?>
	<div class="uk-form-row">
		<fieldset id="parents">
			<legend>Associated Dealerships</legend>
			<?php echo $this->form->render('parents')?>
		</fieldset>
	</div>
<?php endif; ?>