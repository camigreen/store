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
class AccountController extends AppController {

    
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

        // registers tasks
        $this->registerTask('apply', 'save');
    }
    
    /*
            Function: display
                    View method for MVC based architecture

            Returns:
                    Void
    */
    public function display($cachable = false, $urlparams = false) {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        // execute task
        $this->taskMap['display'] = null;
        $this->taskMap['__default'] = null;
        //$this->execute($this->request->get('view','word'));
        $layout = 'accounts';
        $this->user = $this->app->user->get();
        $this->account = $this->table->get(1);

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function account() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }


        

        

       

        
    }

    public function save() {

        // init vars
        $now        = $this->app->date->create();
        $user = $this->app->user->get()->id;
        $aid = $this->app->request->get('aid', 'int');
        $post = $this->app->request->get('post:', 'array', array());

        if($aid) {
            $account = $this->table->get($aid);
        } else {
            $account = $this->app->object->create('account');
        }
        var_dump($post);
        self::bind($account, $post['account']);
        $params = $this->app->parameter->create();

        $account->created = $this->app->date->create($post['account']['created']);
        $account->modified = $now->toSQL();
        $account->modified_by = $user;
        
        foreach($post['account']['params'] as $k => $v) {
            $params->set($k, $v);
        }

        $account->params = $params;

        
        $result = $this->table->save($account);
        $msg = 'Account Saved';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '';
                break;
        }

        $this->setRedirect($link, $msg);

    }
}