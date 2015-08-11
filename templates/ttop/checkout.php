<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$CR = $this->CashRegister;
$this->app->document->addScript('assets:js/jquery-validation-1.13.1/dist/jquery.validate.min.js');
?>
<?php if($this->app->merchant->testMode()) : ?>
<div class="uk-width-1-1 uk-margin">
    <div class="uk-width-1-1 uk-text-center">
        <span class="uk-text-danger uk-text-large testing-mode">TESTING MODE</span>
    </div>
    <?php echo  $CR->order; ?>
</div>
<?php endif; ?>
<div class="uk-clearfix ttop-checkout-title">
    <img src="<?php echo $this->app->path->url('assets:images/shopping_cart_full_128.png'); ?>" class="uk-align-medium-left" />
    <span class="uk-article-title">Checkout</span>
    <div class='uk-align-right'>
        <!-- (c) 2005, 2015. Authorize.Net is a registered trademark of CyberSource Corporation --> <div class="AuthorizeNetSeal"> <script type="text/javascript" language="javascript">var ANS_customer_id="d3b7044f-3c16-4fd1-9a4e-708ced7f70c0";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Credit Card Services</a> </div> 
    </div>
</div>
<div class="uk-width-1-1 uk-margin-bottom ttop-checkout-steps" data-uk-grid-margin>
    <ul class="uk-grid ttop-checkout-progress">
        <li class="uk-width-1-4">
            <div id="customer" class="<?php echo $CR->page->pageStatus('customer'); ?>" >Customer<i class="uk-icon-arrow-right uk-align-right"></i></div>
        </li>
        <li class="uk-width-1-4">
            <div id="payment" class="<?php echo $CR->page->pageStatus('payment'); ?>">Payment Info<i class="uk-icon-arrow-right uk-align-right"></i></div>
        </li>
        <li class="uk-width-1-4">
            <div id="confirm" class="<?php echo $CR->page->pageStatus('confirm'); ?>">Confirm Order<i class="uk-icon-arrow-right uk-align-right"></i></div>
        </li>
        <li class="uk-width-1-4">
            <div id="receipt" class="<?php echo $CR->page->pageStatus('receipt'); ?>">Receipt</div>
        </li>
    </ul>

</div>
<form id="ttop-checkout" class="uk-form" action="?option=com_zoo&controller=store&task=checkout" method="post">
    <div class="uk-width-1-1 uk-margin uk-text-center ttop-checkout-pagetitle">
        <div class="uk-article-title"><?php echo $CR->page->title; ?></div>
        <div class="uk-article-lead"><?php echo $CR->page->subtitle; ?></div>
    </div>
    <div class="uk-width-1-1 uk-text-center ttop-checkout-validation-errors">
        
    </div>
    <?php echo $this->partial($CR->page->id,compact('CR')); ?>
    <div class="uk-width-1-2 uk-container-center uk-margin-top">
        <div class="uk-grid">
            <?php if ($CR->page->buttons['back']['active']) : ?>
            <div class="uk-width-1-2 uk-container-center">
                <button id="back" class="uk-width-1-1 uk-button uk-button-primary ttop-checkout-step-button" data-step="<?php echo $CR->page->buttons['back']['action']; ?>" <?php echo ($CR->page->buttons['back']['disabled'] ? 'disabled' : '') ?>><?php echo $CR->page->buttons['back']['label']; ?></button>
            </div>
            <?php endif; ?>
            <?php if ($CR->page->buttons['proceed']['active']) : ?>
            <div class="uk-width-1-2 uk-container-center">
                <button id="proceed" class="uk-width-1-1 uk-button uk-button-primary ttop-checkout-step-button" data-step="<?php echo $CR->page->buttons['proceed']['action']; ?>" <?php echo ($CR->page->buttons['proceed']['disabled'] ? 'disabled' : '') ?>><?php echo $CR->page->buttons['proceed']['label']; ?></button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <input type="hidden" name="page" value="<?php echo $CR->page->id; ?>" />
    <input type="hidden" name="updated" value="false" />
    <input type="hidden" name="process" value="true" />
    <input type="hidden" name="step" />
    <input type="hidden" name="orderID" />
</form>

<div id="processing-modal" class="uk-modal ttop-checkout-processing-modal">
    <div class="uk-modal-dialog ">
        <div class="uk-vertical-align" style="height:110px">
            <div class="uk-width-1-1 uk-text-center uk-vertical-align-middle ttop-checkout-processing-modal-content">
                <span><i class="uk-icon-spinner uk-icon-spin"></i>Processing</span>
            </div>
        </div>
        
        
    </div>
</div>

<div id="thankyou-modal" class="uk-modal ttop-checkout-thankyou-modal">
    <div class="uk-modal-dialog ">
        <div class="uk-vertical-align" style="height:200px">
            <div class="uk-width-1-1 uk-text-center uk-vertical-align-middle ttop-checkout-thankyou-modal-content">
                <p class="uk-article-title">Your transaction has been approved!</p>
                <p class="uk-article-lead">Thank you for your business.</p>
                <p class="uk-article-lead">Please standby for your receipt.</p>
            </div>
        </div>
        
        
    </div>
</div>

<script type="text/javascript">

//  var _gaq = _gaq || [];
// 
//  
//  
//  (function() {
//    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
//    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
//    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
//  })();

</script>


<script>
    jQuery(function($) {
        function sendTransactionToGoogle(data) {
            var trans = [
                '_addTrans',
                data.transfer.order_number,           // transaction ID - required
                'T-Top Boat Covers',  // affiliation or store name
                parseFloat(data.response.amount),          // total - required
                parseFloat(data.response.tax),           // tax
                parseFloat(data.response.freight)              // shipping
            ];
            var items = [];
            $.each(data.transfer.items,function(k,v){
                var item = [
                    '_addItem',
                    data.transfer.order_number,
                    k,
                    v.name,
                    v.description,
                    parseFloat(v.price),
                    v.qty
                ];
                _gaq.push(item);
                
            });
            console.log(trans);
            console.log(items);
            _gaq.push(['_setAccount', 'UA-2871759-6']);
            _gaq.push(['_trackPageview']);
            _gaq.push(trans);
            _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
        }
        function copyToShipping () {
            var billing = $('fieldset#billing');
            var shipping = $('fieldset#shipping');
            
            billing.find('input, select').each(function(k,v){
                var bName = $(this).prop('name');
                var sName = bName.replace('customer[billing]','customer[shipping]');
                if($(this).is('select')) {
                    shipping.find('select[name="'+sName+'"]').val($(this).val());
                } else {
                    shipping.find('input[name="'+sName+'"]').val($(this).val());
                }
                
            });
        }
        function ProcessingModal (state) {
            var modal = UIkit.modal("#processing-modal",{center:true,bgclose: false});
                
            if (state === 'hide') {
                modal.hide();
            } else {
                modal.show();
            }
        }
        function thankYouModal (state) {
            var modal = UIkit.modal("#thankyou-modal",{center:true,bgclose: false});
                
            if (state === 'hide') {
                modal.hide();
            } else {
                modal.show();
            }
        }
        function verifyCard() {
                console.log('Verifying Card');
                var ccImg = $('.cc-img');
                ccImg.fadeOut();
                ccImg.prop('class','cc-img');
                $('.ttop-checkout-validation-errors').html('');
                var button = $('button#proceed');
                button.html('<i class="uk-icon-spinner uk-icon-spin"></i> Checking Card').prop('disabled',true);
                $.ajax({
                    type: 'POST',
                    url: "?option=com_zoo&controller=store&task=authorizeCard&format=json",
                    data: $('form#ttop-checkout').serialize(),
                    success: function(data){
                        console.log(data);
                        ccImg.addClass(data.card_type);
                        ccImg.fadeIn();
                        if(data.approved) {
                            button.html('Proceed').prop('disabled',false);
                        } else {
                            $('.ttop-checkout-validation-errors').html(data.response.response_reason_text)
                            ccImg.addClass('none');
                            button.html('Proceed');
                        }
                        $('[name="payment[creditCard][cardNumber]"]').val(data.response.account_number);
                        $('[name="payment[creditCard][card_name]"]').val(data.card_name);
                        $('[name="payment[creditCard][card_type]"]').val(data.card_type);
                        $('[name="payment[creditCard][auth_code]"]').val(data.response.transaction_id);
                        var transfer = data.transfer.substring(1, data.transfer.length-1);     
                        $('[name="transfer"]').val(transfer.replace(/\\/g, ""));
                    },
                    error: function(data, status, error) {
                        console.log(status);
                        console.log(error);
                    },
                    dataType: 'json'
                });
        }
        function processPayment() {
                ProcessingModal('show');
                return $.ajax({
                    type: 'POST',
                    url: "?option=com_zoo&controller=store&task=processPayment&format=json",
                    data: $('form#ttop-checkout').serialize(),
                    dataType: 'json'
                }).promise();
        }

        function localPickup() {
            if($('#localPickup').is(':checked')) {
                $.each($('fieldset#shipping input'),function(k,v) {
                    $(this).addClass('ignore');
                })
                $('[name="customer[localPickup]"]').val(1);
            } else {
                $.each($('fieldset#shipping input'),function(k,v) {
                    $(this).removeClass('ignore');
                })
                $('[name="customer[localPickup]"]').val(0);
            }
        }
        
        $(document).ready(function(){

            $('#ttop-checkout').FormHandler({
                validate: true,
                confirm: true,
                debug: true,
                events: {
                    onInit: [
                        function (e) {
                            var self = this;
                            $('#proceed.ttop-checkout-step-button').unbind("click").on('click',$.proxy(this,'_submit'));
                            $('[name="same_as_billing"]').on('click',function(e) {
                                var target = $(e.target);
                                if(target.is(':checked')) {
                                        $('fieldset#billing input').on('input',function(){
                                        copyToShipping();
                                        });
                                        $('fieldset#billing select').on('change',function(){
                                        copyToShipping();
                                        });
                                        copyToShipping();
                                        self.trigger('onChanged',e);
                                    } else {
                                        $('fieldset#billing').off('input').off('changed');
                                    }
                            });
                            $('#back.ttop-checkout-step-button').unbind("click").on("click",function(e){
                                e.preventDefault();
                                $('[name="process"]').val(false);
                                $('input[name="step"]').val($(e.target).data('step'));
                                $(this).closest('form').submit();
                            });

                            $('.update-qty').on('click',function(e){
                                var elem = $(this), sku = $(this).closest('tr').prop('id'), qty = $(this).closest('tr').find('input[name="qty"]').val();
                                e.preventDefault();
                                ProcessingModal();
                                $.ajax({
                                    type: 'POST',
                                    url: "?option=com_zoo&controller=store&task=cart&format=json",
                                    data: {job: 'updateQty', sku: sku, qty: qty},
                                    success: function(data){
                                        console.log(data);
                                        $('input[name="step"]').val('payment');
                                        elem.closest('form').submit();
                                    },
                                    error: function(data, status, error) {
                                        console.log(status);
                                        console.log(error);
                                    },
                                    dataType: 'json'
                                });
                            })
                            $('.trash-item').on('click',function(e){
                                var elem = $(this), sku = $(this).closest('tr').prop('id');
                                e.preventDefault();
                                ProcessingModal();
                                $.ajax({
                                    type: 'POST',
                                    url: "?option=com_zoo&controller=store&task=cart&format=json",
                                    data: {job: 'remove', sku: sku},
                                    success: function(data){
                                        console.log(data);
                                        $('input[name="step"]').val('payment');
                                        elem.closest('form').submit();
                                    },
                                    error: function(data, status, error) {
                                        console.log(status);
                                        console.log(error);
                                    },
                                    dataType: 'json'
                                });
                                
                                
                            });
                            $('button.ttop-checkout-printbutton').on('click',function(e){
                                e.preventDefault();
                                console.log($(this).data('id'));
                                $.ajax({
                                    type: 'POST',
                                    url: "?option=com_zoo&controller=store&task=receipt&format=json",
                                    data: {id: $(this).data('id')},
                                    success: function(data){
                                        console.log(data);
                                        window.open(data.url);
                                    },
                                    error: function(data, status, error) {
                                        console.log(status);
                                        console.log(error);
                                    },
                                    dataType: 'json'
                                });
                                
                                
                            });
                            this.validation = this.$element.validate({
                                ignore: '.ignore',
                                errorClass: "validation-fail"
                            });
                            $('#localPickup').on('click',function(e){
                                localPickup();
                                self.trigger('validate');
                            });
                            localPickup();
                            return true;
                        }
                    ],
                    beforeSubmit: [
                        function (e) {
                            var dfd = $.Deferred();
                            if ($(e.target).data('step') === 'processPayment') {
                                if (!$('[name="TC_Agree"]').prop('checked')) {
                                    alert('Please read and agree to the terms and conditions.');
                                    return false;
                                }
                                $.when(processPayment()).done(function(data){   
                                    if (data.approved) {
//                                        sendTransactionToGoogle(data);
                                        $('input[name="step"]').val('receipt');
                                        $('input[name="orderID"]').val(data.orderID);
                                        ProcessingModal('hide');
                                        thankYouModal('show');
                                        setTimeout(function(){
                                            dfd.resolve(true);
                                        },5000);
                                    } else {
                                        $( ".ttop-checkout-validation-errors" ).html( data.response.response_reason_text );
                                        ProcessingModal('hide');
                                        alert(data.response.response_reason_text);
                                        dfd.resolve(false);
                                    }
                                });
                            } else {
                                $('input[name="step"]').val($(e.target).data('step'));
                                return true;
                            }
                            return dfd.promise();
                            
                            
                        }
                    ],
                    onChanged: [
                        function(e) {
                            var target = $(e.target);
                            switch (target.prop('name')) {

                            }
                            return true;
                        }
                    ],
                    validate: [
                        function(e) {
                            console.log(this.validation.form());
                            return this.validation.form();
                        }
                    ]
                }
            });
        });
        
    });
    
    
</script>
