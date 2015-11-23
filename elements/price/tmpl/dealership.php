<?php

    $price_list[] = '<ul class="uk-list">';
    foreach($prices as $key => $price) {
        $price_list[] = '<li>'.JText::_('PRICE_'.$key).' - '.$this->app->number->currency($price, array('currency' => 'USD')).'</li>';
    }
    $price_list[] = '</ul>';

    $price_list = implode("", $price_list);

?>
<div id="<?php echo $params['id']; ?>-price">
	<i class="currency"></i>
	<span class="price"><?php echo number_format($prices['markup'], 2, '.', ''); ?></span>
	<a id="price_display" href="#"class="uk-icon-button uk-icon-info-circle uk-text-top" style="margin-left:10px;" data-uk-tooltip title="Click here for pricing info!"></a>
</div>

<script>
	jQuery(function($){
		$(document).ready(function(){
			$('#price_display').on('click', function(e) {
				var select = $('<select name="price_display_select" />').append('<option value="retail">MSRP</option>').append('<option value="discount">Dealer Price</option>');
				select.val('discount').change();
				console.log(select);
				var modal = $('<article class="uk-article" />')
					.append('<p class="uk-article-title">Pricing Options</p>')
					.append('<p class="uk-article-lead">These are the current pricing options.</p>')
					.append('<?php echo $price_list; ?>')
					.append('<hr class="uk-article-divider">');
				
				UIkit.modal.confirm(modal.prop('outerHTML'), function(){
				});
				
			})
		})
		
	})

</script>