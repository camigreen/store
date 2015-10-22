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
class UserController extends AppController {

    
    public function __construct($default = array()) {
        parent::__construct($default);


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

        $this->title = 'User Search';
        $db = $this->app->database;
        $query = "SELECT * FROM #__users" ;
        $ids = $db->queryResultArray($query);
        $this->users = array();
        foreach ($ids as $id) {
            $user = $this->app->suser->get($id);
            if ($user->name != 'Super User' && $user->status != 4) {
                $this->users[$id] = $user;
            } 
            
        }

        $layout = 'search';

        $this->getView()->addTemplatePath($this->template->getPath().'/user')->setLayout($layout)->display();
    }

    public function edit() {


        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $uid = $this->app->request->get('uid', 'int');


        if($uid) {
            $this->user = $this->app->suser->get($uid);
            $this->title = 'Edit User';
        } else {
            $type = $this->app->request->get('type', 'string');
            $this->user = $this->app->suser->create();
            $this->user->type = $type;
            $this->title = 'New User';
        }

        $type = $this->user->type;

        $this->form = $this->app->form->create(array($this->template->getPath().'/user/config.xml', compact('type')));
        $this->form->setValues($this->user);

        $layout = 'edit';

        $this->getView()->addTemplatePath($this->template->getPath().'/user')->setLayout($layout)->display();

    }

    public function add () {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $this->title = 'Choose a User Type';
        $layout = 'add';

        $this->getView()->addTemplatePath($this->template->getPath().'/user');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function save() {

        // check for request forgeries
        $this->app->session->checkToken() or jexit('Invalid Token');

        // init vars
        $now        = $this->app->date->create();
        $cUser = $this->app->user->get()->id;
        $uid = $this->app->request->get('uid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $tzoffset   = $this->app->date->getOffset();
        $new = $uid < 1;
        $type = $this->app->request->get('type', 'string');

        if($uid) {
            $user = $this->app->suser->get($uid);
        } else {
            $user = $this->app->suser->create();
        }

        $user->bind($post['core']);

        foreach ($post['elements'] as $key => $value) {
            $user->elements->set($key, $value);
        }

        $user->elements->set('type', $type);



        // $_groups = JUserHelper::getUserGroups($user->id);
        // $emp_groups = array(10, 11);
        // $groups = array();

        // foreach($_groups as $group) {
        //     if(!in_array($group, $emp_groups)) {
        //         $groups[] = $group;
        //     }
        // }
        // $groups[] = $user['group'];


        //JUserHelper::setUserGroups($user->id, $groups);

        if($new || !$user->elements->get('created') || !$user->elements->get('created_by')) {
            $user->elements->set('created', $now->toSQL());
            $user->elements->set('created_by', $cUser);
        }

        // Set Modified Date
        $user->elements->set('modified', $now->toSQL());
        $user->elements->set('modified_by', $cUser);

        $saved = $this->app->suser->save($user) ? true : false;

        if(!$saved) {
            $msg = 'An error occurred while saving.';
            $this->setRedirect($this->baseurl, $msg);
        }

        $msg = $user->name.' has been successfully saved.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&uid='.$user->id;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
        }

        $this->setRedirect($link, $msg);

    }

    public function delete() {
        $uid = $this->app->request->get('uid', 'int');
        $user = $this->app->suser->get($uid);

        $msg = $user->name.' was deleted successfully';

        if($user->superadmin) {
            $msg = 'The user is a super admin and cannot be deleted.';
        }
        
        $user->elements->set('status', 4); 

        $this->app->suser->save($user);
        
        $link = $this->baseurl;

        $this->setRedirect($link, $msg);

    }

    public function resetPassword() {

        $uid = $this->app->request->get('uid','int');

        if(!$user = $this->app->user->get($uid)) {
            return $this->app->error->raiseError(500, JText::_('An error occured while resetting the password.'));
        }
        $new_pwd = JUserHelper::genRandomPassword();
        $user->password = JUserHelper::hashPassword($new_pwd);
        $user->requireReset = 1;
        $user->save();
        $email = $this->app->mail->create();
        $email->setSubject("Password Reset");
        $email->setBody($new_pwd);
        $email->addRecipient($user->email);
        $email->Send();

        $msg = "The users password has been reset.\n The user should receive an email to change thier password.";
        $link = $this->baseurl.'&task=edit&uid='.$uid;
        $this->setRedirect($link,$msg);


    }
}