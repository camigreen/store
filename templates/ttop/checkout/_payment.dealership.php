<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$order = $this->order;
$items = $this->cart->getAllItems();
$elements = $order->elements;
?>
<div class="uk-width-1-1 uk-container-center ttop-checkout-payment">
    <div class="uk-grid">
        <div class='uk-width1-1 items-table'>
            <table class="uk-table">
                <thead>
                    <tr>
                        <th class="uk-width-5-10">Item Name</th>
                        <th class="uk-width-2-10">Quantity</th>
                        <th class="uk-width-1-10">Dealer Markup Price</th>
                        <th class="uk-width-1-10">MSRP</th>
                        <th class="uk-width-1-10">Dealer Price</th>
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
                            <td class="ttop-checkout-item-total">
                                <input type="number" class="uk-width-1-3 uk-text-center" name="qty" value="<?php echo $item->qty ?>" min="1"/>
                                <button class="uk-button uk-button-primary update-qty">Update</button>                
                            </td>
                            <td class="ttop-checkout-item-total">
                                <?php echo $item->getTotal('markup', true); ?>
                                <?php echo '(@ 5% Markup)'; ?>
                            </td>
                            <td class="ttop-checkout-item-total">
                                <?php echo $item->getTotal('retail', true); ?>
                            </td>
                            <td class="ttop-checkout-item-total">
                                <?php echo $item->getTotal('discount', true); ?>
                            </td>
                        </tr>
            <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="uk-text-right">
                            Subtotal:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->subtotal,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="uk-text-right">
                            Shipping:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->ship_total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="uk-text-right">
                            Sales Tax:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->tax_total,array('currency' => 'USD')); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="uk-text-right">
                            Total:
                        </td>
                        <td>
                            <?php echo $this->app->number->currency($order->total,array('currency' => 'USD')); ?>
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
                        <label>Customer Name</label>
                        <input type="text" name="elements[payment][customer_name]" class="ttop-checkout-field required" value='<?php echo $elements->get('payment.customer_name') ?>'/>
                        <label>P.O. Number:</label>
                        <input type="text" name="elements[payment][po_number]" class="ttop-checkout-field" value='<?php echo $elements->get('payment.po_number') ?>'/>
                    </div>
                    <div class="uk-width-1-1">

                    </div> 

                </div>
            </fieldset>
        </div>
    </div>
</div>