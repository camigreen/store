<div class="ttop ttop-account-select-type">
	<div class="uk-width-1-1">
		<div class="uk-article-title uk-text-center uk-margin-bottom">
			<?php echo $this->title; ?>
		</div>
	</div>
	<ul class="uk-grid uk-grid-width-1-4 uk-text-center">
	    <li><button class="uk-button uk-button-primary uk-width-1-2" data-type="dealership">Dealership</button></li>
	    <li><button class="uk-button uk-button-primary uk-width-1-2" data-type="oem">OEM</button></li>
	    <li><button class="uk-button uk-button-primary uk-width-1-2" data-type="user.salesman">Salesman</button></li>
	    <li><button class="uk-button uk-button-primary uk-width-1-2" data-type="user.dealer">Dealer</button></li>
	</ul>
	<form id="account_type" method="post" action="<?php echo $this->baseurl; ?>">
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="type" value="default" />
	</form>
	<script>
		jQuery(function($) {

			$(document).ready(function(){
				$('ul button').on('click', function(e) {
					var type = $(e.target).data('type');
					var form = document.getElementById('account_type');
					form.type.value = type;
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