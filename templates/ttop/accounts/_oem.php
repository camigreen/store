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
<?php var_dump($this->account->elements->get('poc')); ?>

<?php if($this->form->checkGroup('poc')) : ?>
	<div class="uk-form-row">
		<fieldset id="poc">
			<legend>Point of Contact</legend>
			<?php echo $this->form->render('poc')?>
		</fieldset>
	</div>
<?php endif; ?>