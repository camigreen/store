<?php $this->form->setValues($this->account); ?>
<?php if($this->form->checkGroup('details')) : ?>
	<div class="uk-form-row">
		<fieldset id="details">
			<legend>Details</legend>
			<?php echo $this->form->render('details')?>
		</fieldset>
	</div>
<?php endif; ?>