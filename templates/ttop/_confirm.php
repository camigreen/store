<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$order = $CR->order;
$items = $order->items;
$billing = $order->billing;
$shipping = $order->shipping;
$creditCard = $order->creditCard;
$salesperson = $order->getSalesperson();
$article = JTable::getInstance("content"); 
$article->load(22); // Get Article ID  
?>
<div class="uk-width-2-3 uk-container-center ttop-receipt">
    <div class="uk-grid">
        <div class='uk-width-1-1'>
            <p class='uk-text-center'>Orders are shipped within 5-15 business days.</p>

        </div>
        <div class="uk-width-1-1 uk-margin-top">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <h3>Bill To:</h3>
                    <div><?php echo $billing->get('firstname').' '.$billing->get('lastname'); ?></div>
                    <div><?php echo $billing->get('address') ?></div>
                    <div><?php echo $billing->get('city').', '.$billing->get('state').'  '.$billing->get('zip') ?></div>
                    <div>Phone: <?php echo $billing->get('phoneNumber') ?></div>
                    <div>Alternate Phone: <?php echo $billing->get('altNumber') ?></div>
                </div>
                <?php echo $order->localPickup; ?>
                <?php if(!$order->localPickup) : ?>
                    <div class="uk-width-1-2">
                        <h3>Ship To:</h3>
                        <div><?php echo $shipping->get('firstname').' '.$shipping->get('lastname'); ?></div>
                        <div><?php echo $shipping->get('address') ?></div>
                        <div><?php echo $shipping->get('city').', '.$shipping->get('state').'  '.$shipping->get('zip') ?></div>
                        <div>Phone: <?php echo $shipping->get('phoneNumber') ?></div>
                        <div>Alternate Phone: <?php echo $shipping->get('altNumber') ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="uk-width-1-1 uk-margin-top">
            <div>E-mail: <?php echo $billing->get('email'); ?></div>
        </div>
        <?php if($salesperson) : ?>
            <div class="uk-width-1-1 uk-margin-top">
                <div>Sales Rep: <?php echo $salesperson; ?></div>
            </div>
        <?php endif; ?>
        <div class="uk-width-1-1 uk-margin-top">
            <div>Delivery Method: <?php $order->localPickup ? 'Local Pickup' : 'UPS Ground' ?></div>
        </div>
        <div class="uk-width-1-1 uk-margin-top">
            <h3>Payment</h3>
            <div>Card Number: <?php echo $creditCard->maskCardNumber(); ?></div>
            <div>Card Expiration: <?php echo $creditCard->getExpDate();?></div>
        </div>
        <div class='uk-width1-1 items-table uk-margin-top'>
            <table class="uk-table uk-table-condensed uk-table-striped uk-table-hover">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($items as $sku => $item) : ?>
                        <tr>
                            <td>
                                <div class="ttop-checkout-item-name"><?php echo $item->name ?></div>
                                <div class="ttop-checkout-item-description"><?php echo $item->description ?></div>
                                <div class="ttop-checkout-item-options"><?php echo $item->getOptions(); ?></div>

                            </td>
                            <td class="ttop-checkout-item-qty">
                                <?php echo $item->qty; ?>
                            </td>
                            <td class="ttop-checkout-item-total">
                                <?php echo $item->getTotal(); ?>
                            </td>
                        </tr>
            <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            
                        </td>
                        <td class="uk-text-right">
                            Subtotal:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->subtotal,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td class="uk-text-right">
                            Shipping:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->ship_total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td class="uk-text-right">
                            Sales Tax:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->tax_total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td class="uk-text-right">
                            Total:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="uk-width-1-1">
            <?php if($order->localPickup) : ?>
                You have chosen the Local Pickup option.  Your item will be available for pickup at our warehouse in North Charleston, SC.  It is located at
                4651 Franchise Street, North Charleston, SC  29418.  Please call ahead during our normal business hours to ensure your items are ready for pickup.
            <?php endif; ?>
        </div>
        <div class="uk-width-1-1 uk-text-center ttop-checkout-validation-errors">
        
        </div>
        <div class="uk-width-1-1 uk-text-center uk-margin-top">
            <h4 class="uk-text-warning">T-Top Boat Covers holds the right to adjust product pricing for any reason.</h4>
        </div>
        <div class="uk-width-1-1 uk-text-center">
            <input type="checkbox" name="TC_Agree" /><span style="margin-left:10px;">I agree with <a href="#terms_and_conditions" data-uk-modal>terms and conditions.</a></span>
        </div>
        <div id="terms_and_conditions" class="uk-modal">
            <div class="uk-modal-dialog">
                <div class="uk-modal-dialog">
                    <div class="uk-overflow-container">
                        <div style="margin-top: 10px;">
                            
                        </div>
                        <?php 
                            echo $article->get('introtext');
                        ?>
                    </div>
                    <div class="uk-grid uk-margin-top">
                        <div class="uk-width-1-2">
                            <button class="uk-button uk-button-primary uk-width-1-1" name="agree">I agree</button>
                        </div>
                        <div class="uk-width-1-2">
                            <button class="uk-button uk-button-primary uk-width-1-1" name="disagree">I Disagree</button>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function($) {
        $('[name="agree"]').on('click', function(e){
            e.preventDefault();
            $('[name="TC_Agree"]').prop('checked', true);
            var modal = UIkit.modal(".terms_and_conditions");

            if ( modal.isActive() ) {
                modal.hide();
            } else {
                modal.show();
            }
        })
        $('[name="disagree"]').on('click', function(e){
            e.preventDefault();
            $('[name="TC_Agree"]').prop('checked', false);
            var modal = UIkit.modal(".terms_and_conditions");

            if ( modal.isActive() ) {
                modal.hide();
            } else {
                modal.show();
            }
        })
    })
</script>




