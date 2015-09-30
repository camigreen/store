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
        $this->registerTask('save2new', 'save');
        $this->registerTask('cancel', 'display');
        $this->registerTask('upload', 'upload');
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

    public function upload() {
        $path = 'media/zoo/applications/store/images/';
        $this->app->document->setMimeEncoding('application/json');
        $file_parts = explode('.',$_FILES['files']['name'][0]);
        $file_ext = array_pop($file_parts);
        $uuid = $this->app->request->get('uuid','word', null);
        $uuid = $uuid ? $uuid : $this->app->utility->generateUUID();
        $file = $path.$uuid.'.'.$file_ext;
        JFile::upload($_FILES['files']['tmp_name'][0], $file);
        $result = array(
            'file' => '/'.$file,
            'UUID' => $uuid, 

        );
        echo json_encode($result);
    }

    public function edit() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $aid = $this->app->request->get('aid', 'int');
        $type = $this->app->request->get('type', 'word');
        $edit = $aid > 0;
        

        if($edit) {
            if(!$this->account= $this->table->get($aid, $type)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access an account with the id of %s', $aid));
                return;
            }
            $this->title = "Edit Account";
            $subAccounts = array();
            foreach ($this->account->getSubAccounts('oem') as $account) {
                $subAccounts[$account->type][] = $account->id;
            }
            $this->account->params->set('sub-accounts.', $subAccounts);
        } else {
            $type = $this->app->request->get('type', 'word', 'default');
            $this->account = $this->app->account->create($type);
            $this->title = "Create a New $type Account";
            
        }
        $this->form = $this->app->form->create(array($this->app->path->path('classes:accounts/config.xml'), $type));
        $this->form->setValues($this->account);
        $layout = 'edit';
        
        $this->groups = $this->form->getGroups();
         
        $this->getView()->addTemplatePath($this->template->getPath().'/accounts');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function add () {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $this->title = 'Choose an Account Type';
        $layout = 'add';

        $this->getView()->addTemplatePath($this->template->getPath().'/accounts');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function save() {

        // init vars
        $now        = $this->app->date->create();
        $cUser = $this->app->user->get()->id;
        $aid = $this->app->request->get('aid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $tzoffset   = $this->app->date->getOffset();
        $type = $this->app->request->get('type', 'word', 'default');

        if($aid) {
            $account = $this->table->get($aid, $type);
        } else {
            $account = $this->app->account->create($type);
            $account->created_by = $cUser;
        }

        if($type == 'employee') {
            $user = $post['core'];
            $user['id'] = $post['elements']['user'];
            $this->saveUser($user);
        }

        $core = $post['core'];

        self::bind($account, $core);

        $params = $this->app->parameter->create();

        foreach($post['params'] as $key => $value) {
            $params->set($key.'.', $value);
        }

        $account->params = $params;

        $elements = $this->app->parameter->create();

        foreach($post['elements'] as $key => $value) {
            $elements->set($key, $value);
        }

        $account->elements = $elements;

        // Set Created Date
        try {
            $account->created = $this->app->date->create($account->created, $tzoffset)->toSQL();
        } catch (Exception $e) {
            $account->created = $now->toSQL();
        }

        // Set Modified Date
        $account->modified = $now->toSQL();
        $account->modified_by = $cUser;

        
        $result = $this->table->save($account);
        $msg = 'The account has been successfully saved.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&aid='.$account->id.'&type='.$account->type;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
        }

        $this->setRedirect($link, $msg);

    }

    public function saveUser($user) {
        if($user['id']) {
            $_user = $this->app->user->get($user->id); 
        } else {
            $_user = new JUser;
        }

        self::bind($_user, $user);

        $_user->password = JUserHelper::hashPassword($user->password);

        $_user->block = $user['state'] > 1;

        $_user->save();
    }
}