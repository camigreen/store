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
        $this->registerTask('edit', 'edit');
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
        $this->accounts = $this->app->table->account->all();
        $this->title = "Accounts";
        $this->record_count = count($this->accounts);
        
        // Check ACL
        // if (!$this->account->canAccess($this->userprofile->user)) {
        //     return $this->app->error->raiseError(403, JText::_('Unable to access this account'));
        // }
        $layout = 'search';
        $this->getView()->addTemplatePath($this->template->getPath().'/accounts');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function edit() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $aid = $this->app->request->get('aid', 'int');
        $edit = $aid > 0;

        if($edit) {
            if(!$this->account= $this->table->get($aid)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access an account with the id of %s', $aid));
                return;
            }
            $this->account->canEdit();
            // // check ACL
            // if (!$this->account->canEdit()) {
            //     throw new ItemControllerException("Invalid access permissions", 1);
            // }
        }

        $layout = 'edit';

        $this->getView()->addTemplatePath($this->template->getPath().'/accounts');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function save() {

        // init vars
        $now        = $this->app->date->create();
        $user = $this->app->user->get()->id;
        $aid = $this->app->request->get('aid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $tzoffset   = $this->app->date->getOffset();

        if($aid) {
            $account = $this->table->get($aid);
        } else {
            $account = $this->app->account->create('dealer');
        }

        self::bind($account, $post['account']);

        $params = $this->app->parameter->create();

        $account->created = $this->app->date->create($account->created)->toSQL();
        $account->modified = $now->toSQL();
        $account->modified_by = $user;
        
        foreach($post['account']['params'] as $k => $v) {
            $params->set($k, $v);
        }

        $account->params = $params;

        
        $result = $this->table->save($account);
        $msg = 'The account has been successfully saved.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '';
                break;
        }

        $this->setRedirect($link, $msg);

    }
}