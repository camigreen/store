<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$order = $CR->order;
$states = new SimpleUPS\PostalCodes();
?>

<div class="uk-width-2-3 uk-container-center"> 
        <div class="uk-width-1-1">
            <fieldset id="billing">
                <div class="uk-grid" data-uk-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-grid" data-uk-grid-match>
                            <div class="uk-width-1-1">
                                <legend>
                                    Billing Address 
                                </legend>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="billing[firstname]" class="ttop-checkout-field required" placeholder="First Name" value="<?php echo $order->get('billing.firstname'); ?>"/>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="billing[lastname]" class="ttop-checkout-field required" placeholder="Last" value="<?php echo $order->get('billing.lastname'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="billing[address]" class="ttop-checkout-field required"  placeholder="Address" value="<?php echo $order->get('billing.address'); ?>"/>
                    </div>
                    <div class="uk-width-5-10">
                        <input type="text" name="billing[city]" class="ttop-checkout-field required"  placeholder="City" value="<?php echo $order->get('billing.city'); ?>"/>
                    </div>
                    <div class="uk-width-2-10">
                        <?php echo $this->app->html->_('select.genericList',$states->getStates('US',true),'billing[state]',array('class' => 'ttop-checkout-field required'),'value','text',$order->get('billing.state'))?>
                    </div>
                    <div class="uk-width-3-10">
                        <input type="text" name="billing[zip]" class="ttop-checkout-field required"  placeholder="Zip" value="<?php echo $order->get('billing.zip'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="billing[phoneNumber]" class="ttop-checkout-field required" placeholder="Phone Number" value="<?php echo $order->get('billing.phoneNumber'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="billing[altNumber]" class="ttop-checkout-field" placeholder="Alternate Phone Number" value="<?php echo $order->get('billing.altNumber'); ?>"/>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="uk-width-1-1">
            <fieldset id="shipping">
                <div class="uk-grid" data-uk-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-grid" data-uk-grid-match>
                            <div class="uk-width-1-1">
                                <legend>
                                    Shipping Address
                                    <div class="uk-form-controls uk-form-controls-text" style="float:right">
                                        <p class="uk-form-controls-condensed">
                                            <input type="checkbox" id="same_as_billing" class="ttop-checkout-field" name="same_as_billing" style="height:15px; width:15px;" />
                                            <label class="uk-text-small uk-margin-left" >Same as billing</label> 
                                        </p>
                                    </div>
                                </legend>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="shipping[firstname]"  class="ttop-checkout-field required" placeholder="First Name" value="<?php echo $order->get('shipping.firstname'); ?>"/>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="shipping[lastname]"  class="ttop-checkout-field required" placeholder="Last" value="<?php echo $order->get('shipping.lastname'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="shipping[address]"  class="ttop-checkout-field required" placeholder="Address" value="<?php echo $order->get('shipping.address'); ?>"/>
                    </div>
                    <div class="uk-width-5-10">
                        <input type="text" name="shipping[city]"  class="ttop-checkout-field required" placeholder="City" value="<?php echo $order->get('shipping.city'); ?>"/>
                    </div>
                    <div class="uk-width-2-10">
                        <?php echo $this->app->html->_('select.genericList',$states->getStates('US',true),'shipping[state]',array('class' => 'ttop-checkout-field required'),'value','text',$order->get('shipping.state'))?>
                    </div>
                    <div class="uk-width-3-10">
                        <input type="text" name="shipping[zip]"  class="ttop-checkout-field required" placeholder="Zip" value="<?php echo $order->get('shipping.zip'); ?>" />
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="shipping[phoneNumber]" class="ttop-checkout-field required" placeholder="Phone Number" value="<?php echo $order->get('shipping.phoneNumber'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="shipping[altNumber]" class="ttop-checkout-field" placeholder="Alternate Phone Number" value="<?php echo $order->get('shipping.altNumber'); ?>"/>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class='uk-width-1-1'>
            <fieldset id="contact-info">
                <legend>
                    Other Information
                </legend>
                <div class="uk-grid" data-uk-margin>

                    <div class="uk-width-1-1">
                        <input type="email" class="uk-width-1-1 ttop-checkout-field required" name="email" placeholder="E-mail Address" value="<?php echo $order->get('email'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="email" class="uk-width-1-1 ttop-checkout-field" name="confirm_email" placeholder="Confirm E-mail Address" value="<?php echo $order->get('confirm_email'); ?>"/>
                    </div>
                    <div class='uk-width-1-1'>
                        <div class='uk-text-large'>Local Pickup</div>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <input id="localPickup" type="checkbox" style="height:15px; width:15px;" <?php echo ($order->get('localPickup') == "1" ? 'checked' : ''); ?>/>
                                <input type="hidden" name="customer[localPickup]" style="height:15px; width:15px;" value="<?php echo $order->get('localPickup',0); ?>"/>
                                <label class="uk-text-small uk-margin-left" >I want to pickup my order at the T-top Covers location in North Charleston, SC.</label> 
                            </p>
                        </div>
                    </div>
                    
                </div>
            </fieldset>
        </div>
</div>