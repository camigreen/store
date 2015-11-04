<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$order = $CR->order;
$items = $this->cart->getAllItems();
$elements = $order->elements;
?>
<div class="uk-width-1-1 uk-container-center ttop-checkout-payment">
    <div class="uk-grid">
        <div class='uk-width1-1 items-table'>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th class="uk-width-3-10">Item Name</th>
                        <th class="uk-width-2-10">Quantity</th>
                        <th class="uk-width-1-10">Price</th>
                        <th class="uk-width-1-10">Remove</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($items as $sku => $item) : ?>
                        <tr id="<?php echo $sku; ?>">
                            <td>
                                <div class="ttop-checkout-item-name"><?php echo $item->name ?></div>
                                <div class="ttop-checkout-item-description"><?php echo $item->description ?></div>
                                <div class="ttop-checkout-item-options"><?php echo $item->getOptions(); ?></div>

                            </td>
                            <td>
                                <input type="number" class="uk-width-1-3 uk-text-center" name="qty" value="<?php echo $item->qty ?>" min="1"/>
                                <button class="uk-button uk-button-primary update-qty">Update</button>                
                            </td>
                            <td>
                                <?php echo $item->getTotal(); ?>
                            </td>
                            <td>
                                <div id="<?php echo $sku; ?>" class="uk-icon-button uk-icon-trash trash-item"></div>
                            </td>
                        </tr>
            <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="uk-text-right">
                            Subtotal:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($CR->subtotal,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="uk-text-right">
                            Shipping:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($CR->shipping,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="uk-text-right">
                            Sales Tax:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($CR->taxTotal,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="uk-text-right">
                            Total:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($CR->total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="uk-width-1-1 uk-text-center uk-margin-top">
            <h4 class="uk-text-warning">T-Top Boat Covers holds the right to adjust product pricing for any reason.</h4>
        </div>
    </div>
</div>
<div class="uk-width-1-2 uk-container-center">
    <div class="uk-grid" data-uk-grid-margin>
        <div class='uk-width-1-1'>
            <fieldset id="payment-info">
                <div class="uk-grid" data-uk-margin>
                    <div class="uk-width-1-1">
                        <legend>
                            Payment Information
                        </legend>
                    </div>
                    <div class="uk-width-1-1">
                        <label>Account Name:</label>
                        <input type="text" name="elements[payment][account_name]" disabled class="ttop-checkout-field required" value='<?php echo $elements->get('payment.account_name'); ?>'/>
                        <label>Account Number:</label>
                        <input type="text" name="elements[payment][account_number]" disabled class="ttop-checkout-field required" value='<?php echo $elements->get('payment.account_number') ?>'/>
                        <label>P.O. Number:</label>
                        <input type="text" name="elements[payment][po_number]" class="ttop-checkout-field required" value='<?php echo $elements->get('payment.po_number') ?>'/>
                    </div>
                    <div class="uk-width-1-1">

                    </div> 
                    <div class="uk-width-1-3">
                        <input type="hidden" name="payment[creditCard][auth_code]" value="<?php echo $elements->get('creditcard.auth_code'); ?>"/>
                        <input type="hidden" name="payment[creditCard][card_type]" value="<?php echo $elements->get('creditcard.card_type'); ?>" />
                        <input type="hidden" name="payment[creditCard][card_name]" value="<?php echo $elements->get('creditcard.card_name'); ?>" />
                        <input type="hidden" name="amount" value="<?php echo $CR->getCurrency('total'); ?>"/>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>