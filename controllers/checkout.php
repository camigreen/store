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

        $this->app->document->addScript('assets:js/formhandler.js');

        $layout = 'checkout';

        $this->page = 'payment';
        $this->title = 'Payment Information';
        $this->subtitle = 'Please enter your payment information below.';
        $this->buttons = array(
            'back' => array(
                    'active' => false
                ),
            'proceed' => array(
                    'active' => true,
                    'action' => 'payment',
                    'disabled' => false,
                    'label' => 'Proceed'
                )
        );

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function confirm() {

    }

    public function reciept() {

    }

    public function save() {

        $order = $this->CR->order;

        $post = $this->app->request->get('post:', 'array', array());

        foreach($post as $key => $value) {
            if(is_array($value)) {
                foreach($value as $k => $v) {
                    $order->set($key.'.'.$k, $v);
                }
            } else {
                $order->$key = $value;
            }
        }

        var_dump($order);

        $this->app->session->set('order',(string) $order,'checkout');

        $next = $this->app->request->get('next','word');

        $this->$next();

    }


}