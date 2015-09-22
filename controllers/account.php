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
        echo $this->app->request->getCmd('view');
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
        $edit = $aid > 0;
        

        if($edit) {
            if(!$this->account= $this->table->get($aid)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access an account with the id of %s', $aid));
                return;
            }
            $type = $this->account->type;
            $this->title = "Edit Account";
            $subAccounts = array();
            foreach ($this->account->getSubAccounts('oem') as $account) {
                $subAccounts[$account->type][] = $account->id;
            }
            $this->account->params->set('sub-accounts.', $subAccounts);
        } else {
            $type = $this->app->request->get('account_type', 'word', 'default');
            $this->account = $this->app->account->create($type);
            $this->title = "Create a New $type Account";
            
        }
        $this->paramform = $this->app->storeparameterform->create($this->app->path->path('classes:accounts/account.xml'), $type);
        $this->paramform->setValues($this->account);
        $layout = 'edit';
        
        $this->groups = $this->paramform->getGroups();
         
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
        $user = $this->app->user->get()->id;
        $aid = $this->app->request->get('aid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $tzoffset   = $this->app->date->getOffset();
        $type = $this->app->request->get('account_type', 'word', 'default');

        if($aid) {
            $account = $this->table->get($aid);
        } else {
            $account = $this->app->account->create($type);
        }

        $core = $post['core'];

        self::bind($account, $core);

        $params = $this->app->parameter->create();

        foreach($post['params'] as $key => $value) {
            $params->set($key.'.', $value);
        }

        $account->params = $params;

        $account->created = $this->app->date->create($account->created)->toSQL();
        $account->modified = $now->toSQL();
        $account->modified_by = $user;

        
        $result = $this->table->save($account);
        $msg = 'The account has been successfully saved.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&aid='.$account->id;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
        }

        $this->setRedirect($link, $msg);

    }
}