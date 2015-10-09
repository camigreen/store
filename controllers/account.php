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
        $order = $this->app->orderdev->get(6153);
        var_dump($order->elements->get('items'));
        // $orders = $this->app->database->queryAssocList('SELECT id FROM joomla_zoo_order');

        // foreach($orders as $order) {

        //     $_order = $this->app->order->create($order['id']);
        //     $this->relayorder($_order);
        // }
        
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

    public function relayorder($order) {

        $orderdev = $this->app->orderdev->create();
        $ignore = array('orderDate','salesperson');
        foreach(get_object_vars($order) as $key => $value) {
            if(in_array($key, $ignore)) {
                continue;
            }
            if(property_exists($orderdev, $key)) {
                $orderdev->$key = $value;
            } else {
                $orderdev->elements->set($key, $value);
            }

        }
        $orderdev->created = $order->orderDate;
        $orderdev->created_by = $order->salesperson;
        $orderdev->modified = $order->orderDate;
        $orderdev->modified_by = $order->salesperson;

        $orderdev->save();
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
        $account_type = $this->app->request->get('type', 'string');
        list($template, $type) = array_pad(explode('.',$account_type, 2), 2, 'default');
        $edit = $aid > 0;
        echo 'Template: '.$template.'</br>';
        echo 'Type: '.$type;
        if($edit) {
            if(!$this->account = $this->table->get($aid, $type == 'default' ? $template : $type)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access an account with the id of %s', $aid));
                return;
            }
            $this->title = "Edit Account";
            $subAccounts = array();
            foreach ($this->account->getSubAccounts('oem') as $subaccount) {
                $subAccounts[$subaccount->type][] = $subaccount->id;
            }
            $this->account->elements->set('subaccounts', $subAccounts);
        } else {
            $this->account = $this->app->account->create($account_type);
            $this->title = $type == 'default' ? "Create a New $template Account" : "Create a New $type Account";
            
        }
        $this->form = $this->app->form->create(array($this->app->path->path('classes:accounts/config.xml'), compact('template', 'type')));
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

        // check for request forgeries
        $this->app->session->checkToken() or jexit('Invalid Token');

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
            $user['group'] = $post['elements']['job_title'];
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

        foreach($post['subaccounts'] as $type => $subaccounts) {
            foreach($subaccounts as $subaccount) {
                $this->app->account->associate($account->id, $subaccount, $type);
            }
        }

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
            $_user = $this->app->user->get($user['id']); 
        } else {
            $_user = new JUser;
        }

        self::bind($_user, $user, array('password'));

        if(!empty($post['core']['password'])) {
            $_user->password = JUserHelper::hashPassword($user->password);
        }

        $_user->block = $user['state'] > 1;

        $_user->save();

        $_groups = JUserHelper::getUserGroups($_user->id);
        $emp_groups = array(10, 11);
        $groups = array();

        foreach($_groups as $group) {
            if(!in_array($group, $emp_groups)) {
                $groups[] = $group;
            }
        }
        $groups[] = $user['group'];


        JUserHelper::setUserGroups($_user->id, $groups);


    }

    public function resetPassword() {
        $aid = $this->app->request->get('aid','int');
        $account = $this->table->get($aid);
        $user_id = $account->elements->get('user');
        if(!$user = $this->app->user->get($user_id)) {
            return $this->app->error->raiseError(500, JText::_('An error occured while resetting the password.'));
        }
        $new_pwd = JUserHelper::genRandomPassword();
        $user->password = JUserHelper::hashPassword($new_pwd);
        $user->save();
        $email = $this->app->mail->create();
        $email->setSubject("Password Reset");
        $email->setBody($new_pwd);
        $email->addRecipient($user->email);
        $email->Send();

        $msg = "The users password has been reset.\n An email has been sent to the user.";
        $link = $this->baseurl.'&task=edit&aid='.$account->id.'&type='.$account->type;
        $this->setRedirect($link,$msg);


    }
}