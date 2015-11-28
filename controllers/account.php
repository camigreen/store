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

        $this->cUser = $this->app->user->get();

        //var_dump($this->app->account->getCurrent());

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
        $options = array();
        $search = $this->app->request->get('search', 'string');
        list($type, $kind) = explode('.', $search, 2);
        var_dump($type);
        var_dump($kind);
        if($type != 'all') {
            $conditions = "type = '$type'";
            $conditions .= $kind != '' ? " AND kind = '$kind'" : '';
            $options['conditions'] = $conditions;
        }

        $this->accounts = $this->app->table->account->all($options);
        $this->title = "Accounts";
        $this->record_count = count($this->accounts);

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
        $edit = $aid > 0;
        if($edit) {
            if(!$this->account = $this->table->get($aid)) {
                $this->app->error->raiseError(500, JText::sprintf('Unable to access an account with the id of %s', $aid));
                return;
            }
            $type = $this->account->getLayout();
            $this->title = "Edit Account";
        } else {
            $this->account = $this->app->account->create();
            $type = $this->app->request->get('type', 'string');
            $this->account->type = $type;
            $this->title = $type == 'default' ? "Create a New $template Account" : "Create a New $type Account";

        }

        $this->form = $this->app->form->create(array($this->template->getPath().'/accounts/config.xml', compact('type')));
        $this->form->setValues($this->account);
        $layout = 'edit';
        $this->type = $type;
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
        
        $aid = $this->app->request->get('aid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $type = $this->app->request->get('type', 'word', 'default');
        echo 'Post</br>';
        var_dump($post);

        if($aid) {
            $account = $this->table->get($aid);
        } else {
            $account = $this->app->account->get();
            $account->type = $type;
            $account->created_by = $cUser;
        }

        $account->bind($post);
        echo 'Bind</br>';
        var_dump($account);

        //self::bind($account, $core);


        // Save to get the ID.
        //$this->table->save($account);
        $account->save();

        var_dump($account);
        return;

        
        $result = $this->table->save($account);
        $msg = 'The account was saved successfully.';
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

    public function mapProfilesToAccount() {
        $profiles = $this->app->request->get('profiles', 'array');
        $aid = $this->app->request->get('aid', 'int');
        $map[$aid] = $profiles;
        $this->app->account->mapProfilesToAccount($map);
        $link = $this->baseurl.'&task=edit&aid='.$aid;
        $msg = 'Profiles added successfully';
        $this->setRedirect($link, $msg);

    }

}