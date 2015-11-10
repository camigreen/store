<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
    Class: DefaultController
        Site controller class
*/
class CheckoutController extends AppController {

    public $account = null;
    public $processCC = 'true';

    
    public function __construct($default = array()) {
        parent::__construct($default);

        // set table
        $this->table = $this->app->table->account;

        // get application
        $this->application = $this->app->zoo->getApplication();

        // get Joomla application
        $this->joomla = $this->app->system->application;

        // get params
        $this->params = $this->joomla->getParams();

        // get pathway
        $this->pathway = $this->joomla->getPathway();

        // set base url
        $this->baseurl = $this->app->link(array('controller' => $this->controller), false);


        $this->cart = $this->app->cart->create();

        if($account = $this->app->account->getCurrent()) {
            $this->account = $account;
        }

        // registers tasks
        $this->registerTask('receipt', 'display');
        $this->registerTask('customer', 'display');
        $this->registerTask('payment', 'display');
        $this->registerTask('confirm', 'display');
        $this->registerTask('save', 'display');
        $this->registerTask('processPO', 'display');
        // $this->taskMap['display'] = null;
        // $this->taskMap['__default'] = null;
    }
    
    /*
            Function: display
                    View method for MVC based architecture

            Returns:
                    Void
    */
    public function display($cachable = false, $urlparams = false) {

        if($this->task != 'receipt') {
            $this->CR = $this->app->cashregister->start();
        }
        $task = $this->task;
        $this->$task();

    }

    public function customer() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $this->app->document->addScript('assets:js/formhandler.js');

        $order = $this->CR->order;

        var_dump($this->CR->getShippingRate());

        $user = $this->app->userprofile->getCurrent();
        $this->page = 'customer';
        if($this->account && $this->account->type != 'store') {
            $this->page .= '.'.$this->account->type;
            $order->elements->set('billing.', $this->account->elements->get('billing'));
            $order->elements->set('billing.phoneNumber', $user->elements->get('office_phone'));
            $order->elements->set('billing.altNumber', $user->elements->get('mobile_phone'));
            $order->elements->set('shipping.', $this->account->elements->get('shipping'));
            $order->elements->set('shipping.phoneNumber', $user->elements->get('office_phone'));
            $order->elements->set('shipping.altNumber', $user->elements->get('mobile_phone'));
            $order->elements->set('email', $user->getUser()->email);
            $order->elements->set('confirm_email', $user->getUser()->email);
        }

        $type = 'customer';

        $this->form = $this->app->form->create(array($this->template->getPath().'/checkout/config.xml', compact('type')));

        $layout = 'checkout';
        
        $this->title = 'Customer Information';
        $this->subtitle = 'Please enter your information below.';
        $this->buttons = array(
            'back' => array(
                    'active' => false
                ),
            'proceed' => array(
                    'active' => true,
                    'next' => 'payment',
                    'disabled' => false,
                    'label' => 'Proceed'
                )
        );

        $this->order = $order;

        $this->getView()->addTemplatePath($this->template->getPath().'/checkout')->setLayout($layout)->display();

    }

    public function payment() {

        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $this->page = 'payment';

        $order = $this->CR->order;

        if($this->account && $this->account->type != 'store') {
            $this->page .= '.'.$this->account->type;
            $order->elements->set('payment.account_name', $this->account->name);
            $order->elements->set('payment.account_number', $this->account->number);
            $this->app->session->set('order',(string) $order,'checkout');
        }

        $this->app->document->addScript('assets:js/formhandler.js');

        $layout = 'checkout';

        
        $this->title = 'Payment Information';
        $this->subtitle = 'Please enter your payment information below.';
        $this->buttons = array(
            'back' => array(
                    'active' => true,
                    'next' => 'customer',
                    'disabled' => false,
                    'label' => 'Back'
                ),
            'proceed' => array(
                    'active' => true,
                    'next' => 'confirm',
                    'disabled' => false,
                    'label' => 'Proceed'
                )
        );

        $this->order = $order;

        $this->getView()->addTemplatePath($this->template->getPath().'/checkout')->setLayout($layout)->display();

    }

    public function confirm() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $this->app->document->addScript('assets:js/formhandler.js');

        $order = $this->CR->order;
        $next = 'processCC';
        $layout = 'checkout';
        $this->page = 'confirm';
        if($this->account && $this->account->type != 'store') {
            $this->page .= '.'.$this->account->type;
            $this->processCC = 'false';
            $next = 'processPO';
            
        }
        

        
        $this->title = 'Order Confirmation';
        $this->subtitle = '<span class="uk-text-danger">Please make sure that your order is correct.</span>';
        $this->buttons = array(
            'back' => array(
                    'active' => true,
                    'next' => 'payment',
                    'disabled' => false,
                    'label' => 'Back'
                ),
            'proceed' => array(
                    'active' => true,
                    'next' => $next,
                    'disabled' => false,
                    'label' => 'Proceed'
                )
        );

        

        

        $this->order = $order;

        $this->getView()->addTemplatePath($this->template->getPath().'/checkout')->setLayout($layout)->display();
    }

    public function receipt() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $id = $this->app->request->get('oid', 'int', 0);
        $order = $this->app->orderdev->get($id);
        $layout = 'checkout';
        $this->page = 'receipt';
        if($this->account && $this->account->type != 'store') {
            $this->page .= '.'.$this->account->type;
            
        }

        $this->title = 'Order Receipt';
        $this->subtitle = 'Thank you for your purchase.';
        $this->buttons = array(
            'back' => array(
                    'active' => false,
                    'next' => 'payment',
                    'disabled' => false,
                    'label' => 'Back'
                ),
            'proceed' => array(
                    'active' => true,
                    'next' => 'home',
                    'disabled' => false,
                    'label' => 'Return to Home Page'
                )
        );
        $this->order = $order;

        $this->getView()->addTemplatePath($this->template->getPath().'/checkout')->setLayout($layout)->display();
    }

    public function processPO () {
        $order = $this->CR->processPayment('PO');

        $link = $this->baseurl.'&task=receipt&oid='.$order->id;
        $this->setRedirect($link);

    }

    public function save() {

        $order = $this->CR->order;
        $tzoffset   = $this->app->date->getOffset();
        $now        = $this->app->date->create();
        $cUser = $this->app->user->get()->id;
        $post = $this->app->request->get('post:', 'array', array());
        $next = $this->app->request->get('next','word', 'customer');

        if(isset($post['elements'])) {
            foreach($post['elements'] as $key => $value) {
                if (is_array($value)) {
                    $order->elements->set($key.'.', $value);
                } else {
                    $order->elements->set($key, $value);
                }
            }
        }
        
        if($lp = $order->elements->get('localPickup')) {
            $order->elements->set('localPickup', ($lp == 'on' ? true : false));
        }

        // Set Created Date
        try {
            $order->created = $this->app->date->create($order->created, $tzoffset)->toSQL();
        } catch (Exception $e) {
            $order->created = $now->toSQL();
        }

        $order->created_by = $cUser;

        // Set Modified Date
        $order->modified = $now->toSQL();
        $order->modified_by = $cUser;

        $this->app->session->set('order',(string) $order,'checkout');



        $this->setRedirect($this->baseurl.'&task='.$next);

    }


}