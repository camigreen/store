<?php 
	$id = $parent->getValue('id');
	$profiles = $this->app->userprofile->getUserAssignments();
	$account = $this->app->account->get($parent->getValue('id'));
	$available = isset($profiles[0]) ? $profiles[0] : array();
	$selected = isset($profiles[$account->id]) ? $profiles[$account->id] : array();

	// echo 'Available:';
	// var_dump($available);
	// echo 'Selected:';
	// var_dump($selected);
?>
<div class="uk-width-1-1">

	<ul class="uk-list uk-list-striped selected-profile-list">
	<?php if(empty($selected)) : ?>
		<li class="empty uk-text-small">There are no users assigned to this account.</li>
	<?php endif; ?>
	<?php foreach($selected as $id => $profile) : ?>
		
		<li id="<?php echo $profile->id; ?>" data-name="<?php echo $profile->getUser()->name; ?>">
			<input type="hidden" name="profiles[]" value="<?php echo $profile->id; ?>" />
			<?php echo $profile->getUser()->name.'<a href="#" class="uk-close uk-float-right uk-text-muted"></a>'; ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<!-- This is a button toggling the modal -->
<a href="#" class="uk-button" data-uk-modal="{target:'#user-modal'}">Add User</a>

<!-- This is the modal -->
<div id="user-modal" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <p>Select Users to add to the account.</p>
        	<ul class="uk-list available-profile-list">
        	<?php if(empty($available)) : ?>
        		<li class="empty uk-text-small">No Available Users Found!</li>
        	<?php endif; ?>
        	<?php foreach($available as $profile) : ?>
        	<li id="<?php echo $profile->id; ?>" data-name="<?php echo $profile->getUser()->name; ?>">
	        	<label>
	    			<input type="checkbox">
	    			<?php echo $profile->getUser()->name; ?>
	    		</label>
    		</li>
    		<?php endforeach; ?>
    		</ul>
    		<a href="#" class="uk-button uk-modal-close">Add User(s)</a>
    </div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		var selected = {}, available = {};
		function getValues() {
			var _selected = $('.selected-profile-list li');
			var _available = $('.available-profile-list li');
			$.each(_selected, function(k,v) {
				var elem = $(v);
				if(!elem.hasClass('empty')) {
					var id = elem.prop('id');
					var name = elem.data('name');
					console.log(elem);
					selected[id] = name;
				}
			})
			console.log(selected);
			$.each(_available, function(k,v) {
				var elem = $(v);
				if(!elem.hasClass('empty')) {
					var id = elem.prop('id');
					var name = elem.data('name');
					console.log(elem);
					available[id] = name;
				}
			})
			console.log(available);

		}
		function populateElements() {
			var _selected = $('.selected-profile-list');
			var _available = $('.available-profile-list');
			_selected.find('li').remove();
			_available.find('li').remove();
			if ($.isEmptyObject(selected)) {
				_selected.append('<li class="empty uk-text-small">There are no users assigned to this account.</li>');
			}
			$.each(selected, function(k,v) {
				var li = $('<li></li>').prop('id', k).data('name', v).html(v+'<a href="#" class="uk-close uk-float-right uk-text-muted"></a>');
				var input = $('<input type="hidden" />').val(k).prop('name', 'profiles[]');
				li.append(input);
				_selected.append(li);
			})
			if ($.isEmptyObject(available)) {
				_available.append('<li class="empty uk-text-small">No Available Users Found!</li>');
			}
			$.each(available, function(k,v) {
				var li = $('<li></li>').prop('id', k).data('name', v);
				var input = $('<input type="checkbox" />');
				var label = $('<label></label>').append(input).append(v);
				li.append(label);
				_available.append(li);
			})
			$('#user-modal a.uk-button').on('click', function() {
				
				var values = {};

				$('.available-profile-list li').each(function(k,v) {
					var elem = $(v);
					var chkbox = $(v).find('input');
					var id = elem.prop('id');
					if(chkbox.is(':checked')) {
						selected[id] = elem.data('name');
					} else {
						values[id] = elem.data('name')
					}
				})
				available = values;
				console.log(available);
				populateElements();
			})
			$('.selected-profile-list li a').on('click',function(e) {
				e.preventDefault();
				var id = $(e.target).closest('li').prop('id');
				console.log(id);
				var values = {};
				$.each(selected, function(k,v) {
					if(k != id) {
						values[k] = v;
					} else {
						available[k] = v
					}
				})
				selected = values;
				console.log(selected);
				populateElements();
			})
		}
		$(document).ready(function() {

			getValues();

			populateElements();
			
		});
	})
</script>