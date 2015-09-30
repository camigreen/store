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
class UserProfileController extends AppController {

    
    public function __construct($default = array()) {
        parent::__construct($default);

        // set table
        $this->table = $this->app->table->userprofile;

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
        $this->users = $this->table->all();
        $this->title = "Users";
        $this->noRecords = count($this->users) < 1;
        
        // Check ACL
        // if (!$this->account->canAccess($this->userprofile->user)) {
        //     return $this->app->error->raiseError(403, JText::_('Unable to access this account'));
        // }


        $layout = 'search';
        $this->getView()->addTemplatePath($this->template->getPath().'/userprofile');

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
        $uid = $this->app->request->get('uid', 'int');
        $edit = $uid > 0;
        

        if($edit) {
            if(!$this->profile = $this->table->get($uid)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access a user with the id of %s', $uid));
                return;
            }
            $this->title = "Edit User";
            $this->user = $this->profile->getUser();
        } else {
            $this->profile = $this->app->object->create('userprofile');
            $this->user = new JUser;
            $this->title = "Create a New User";
            
        }

        echo JHtmlAccess::usergroup('test', array());
        echo JHtmlAccess::assetgrouplist('test', array());

        $this->form = $this->app->form->create($this->app->path->path('classes:userprofile/config.xml'));
        
        $this->user->params = $this->app->parameter->create($this->user->params);
        $this->user->password = null;

        $this->tzoffset = $this->app->date->getOffset();

        $layout = 'edit';
        
        $this->groups = $this->form->getGroups();
         
        $this->getView()->addTemplatePath($this->template->getPath().'/userprofile');

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
        $now = $this->app->date->create();
        $user = $this->app->user->get()->id;
        $uid = $this->app->request->get('uid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $tzoffset   = $this->app->date->getOffset();
        var_dump($post);
        if($uid) {
            $profile = $this->table->get($uid);
            $user = $profile->getUser();
            
        } else {
            $user = new JUser;
            $profile = $this->app->object->create('userprofile');
        }

        self::bind($user, $post['core']);

        $user->password = JUserHelper::hashPassword($user->password);

        $user->block = $post['core']['state'] > 1;

        $user->save();

        $profile->id = $user->id;
        $profile->state = $post['core']['state'];

        $params = $this->app->parameter->create();

        if (isset($post['params'])) {
            foreach($post['params'] as $key => $value) {
                $params->set($key, $value);
            }
        }

        $profile->params = $params;

        $elements = $this->app->parameter->create();

        if(isset($post['elements'])) {
            foreach($post['elements'] as $key => $value) {
                $elements->set($key, $value);
            }
        }

        $profile->elements = $elements;

        if(isset($post['elements']['account']) && $post['elements']['account'] != "0") {
            $account = $this->app->account->get($post['elements']['account']);
            $account->linkUser($user->id);
        }

        // Set Modified Date
        $profile->modified = $now->toSQL();
        $profile->modified_by = $this->app->user->get()->id;

        // Set Created Date
        try {
            $profile->created = $this->app->date->create($profile->created, $tzoffset)->toSQL();
        } catch (Exception $e) {
            $profile->created = $now->toSQL();
        }

        $result = $this->table->save($profile);
        $msg = 'The user has been successfully saved.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&uid='.$profile->id;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
        }

        $this->setRedirect($link, $msg);

    }

    public function resetPassword() {
        $uid = $this->app->request->get('uid','int');
        if(!$user = $this->app->user->get($uid)) {
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
        $link = $this->baseurl.'&task=edit&uid='.$user->id;
        $this->setRedirect($link,$msg);


    }
}