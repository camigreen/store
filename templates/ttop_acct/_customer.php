<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$customer = $CR->get('customer');
$states = new SimpleUPS\PostalCodes();
?>

<div class="uk-width-2-3 uk-container-center">
    <div class="uk-grid" data-uk-grid-margin>
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
                        <input type="text" name="customer[billing][firstname]" class="ttop-checkout-field required" placeholder="First Name" value="<?php echo $customer->billing->firstname; ?>"/>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="customer[billing][lastname]" class="ttop-checkout-field required" placeholder="Last" value="<?php echo $customer->billing->lastname; ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[billing][address]" class="ttop-checkout-field required"  placeholder="Address" value="<?php echo $customer->billing->address;?>"/>
                    </div>
                    <div class="uk-width-5-10">
                        <input type="text" name="customer[billing][city]" class="ttop-checkout-field required"  placeholder="City" value="<?php echo $customer->billing->city; ?>"/>
                    </div>
                    <div class="uk-width-2-10">
                        <?php echo $this->app->html->_('select.genericList',$states->getStates('US',true),'customer[billing][state]',array('class' => 'ttop-checkout-field required'),'value','text',$customer->billing->state)?>
                    </div>
                    <div class="uk-width-3-10">
                        <input type="text" name="customer[billing][zip]" class="ttop-checkout-field required"  placeholder="Zip" value="<?php echo $customer->billing->zip; ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[billing][phoneNumber]" class="ttop-checkout-field required" placeholder="Phone Number" value="<?php echo $customer->billing->phoneNumber; ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[billing][altNumber]" class="ttop-checkout-field" placeholder="Alternate Phone Number" value="<?php echo $customer->billing->altNumber; ?>"/>
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
                        <input type="text" name="customer[shipping][firstname]"  class="ttop-checkout-field required" placeholder="First Name" value="<?php echo $customer->shipping->firstname; ?>"/>
                    </div>
                    <div class="uk-width-1-2">
                        <input type="text" name="customer[shipping][lastname]"  class="ttop-checkout-field required" placeholder="Last" value="<?php echo $customer->shipping->lastname; ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[shipping][address]"  class="ttop-checkout-field required" placeholder="Address" value="<?php echo $customer->shipping->address; ?>"/>
                    </div>
                    <div class="uk-width-5-10">
                        <input type="text" name="customer[shipping][city]"  class="ttop-checkout-field required" placeholder="City" value="<?php echo $customer->shipping->city; ?>"/>
                    </div>
                    <div class="uk-width-2-10">
                        <?php echo $this->app->html->_('select.genericList',$states->getStates('US',true),'customer[shipping][state]',array('class' => 'ttop-checkout-field required'),'value','text',$customer->shipping->state)?>
                    </div>
                    <div class="uk-width-3-10">
                        <input type="text" name="customer[shipping][zip]"  class="ttop-checkout-field required" placeholder="Zip" value="<?php echo $customer->shipping->zip; ?>" />
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[shipping][phoneNumber]" class="ttop-checkout-field required" placeholder="Phone Number" value="<?php echo $customer->shipping->phoneNumber; ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="text" name="customer[shipping][altNumber]" class="ttop-checkout-field" placeholder="Alternate Phone Number" value="<?php echo $customer->shipping->altNumber; ?>"/>
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
                        <input type="email" class="uk-width-1-1 ttop-checkout-field required" name="customer[email]" placeholder="E-mail Address" value="<?php echo $customer->get('email'); ?>"/>
                    </div>
                    <div class="uk-width-1-1">
                        <input type="email" class="uk-width-1-1 ttop-checkout-field" name="customer[confirm_email]" placeholder="Confirm E-mail Address" value="<?php echo $customer->get('confirm_email'); ?>"/>
                    </div>
                    <div class='uk-width-1-1'>
                        <div class='uk-text-large'>Local Pickup</div>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <input type="checkbox" name="local_pickup" style="height:15px; width:15px;" <?php echo ($CR->get('localPickUp') ? 'checked' : ''); ?>/>
                                <label class="uk-text-small uk-margin-left" >I want to pickup my order at the T-top Covers location in North Charleston, SC.</label> 
                            </p>
                        </div>
                    </div>
                    
                </div>
            </fieldset>
        </div>
    </div>
</div>