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

        $this->CR = $this->app->cashregister->start();

        $this->cart = $this->app->cart->create();

        // registers tasks
        //$this->registerTask('apply', 'save');
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

    }

    public function customer() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $this->app->document->addScript('assets:js/formhandler.js');

        $this->app->account->getByUser();

        $layout = 'checkout';
        $this->page = 'customer';
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

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function payment() {

        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        var_dump($this->app->userprofile->get()->getUser());

        $this->app->document->addScript('assets:js/formhandler.js');

        $layout = 'checkout';

        $this->page = 'payment';
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

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function confirm() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $this->app->document->addScript('assets:js/formhandler.js');

        $layout = 'checkout';

        $this->page = 'confirm';
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
                    'next' => 'reciept',
                    'disabled' => false,
                    'label' => 'Proceed'
                )
        );

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function reciept() {

    }

    public function save() {

        $order = $this->CR->order;
        $tzoffset   = $this->app->date->getOffset();
        $now        = $this->app->date->create();
        $cUser = $this->app->user->get()->id;
        $post = $this->app->request->get('post:', 'array', array());
        $next = $this->app->request->get('next','word', 'customer');

        foreach($post['elements'] as $key => $value) {
            if (is_array($value)) {
                $order->elements->set($key.'.', $value);
            } else {
                $order->elements->set($key, $value);
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



        $this->$next();

    }


}