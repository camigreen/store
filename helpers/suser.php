<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// $zoo = App::getInstance('zoo');
// $zoo->loader->register('UserAppHelper', 'helpers:user.php');
/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class SUserHelper extends UserAppHelper {

    public $users = array();

    public function getName() {
        return 'suser';
    }

    public function getStatus($user) {
        
        $status = $user->elements->get('status');

        return $this->app->status->get('user',$status);

    }

    public function get($id = null) {

        $user = parent::get($id);
        
        $app = $this->app;

        // trigger init event
        $this->app->event->dispatcher->notify($this->app->event->create($user, 'user:init', compact('app')));

        return $user;
    }

    public function all() {
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

        return $this->users;
    }

    public function create() {

        $user = new JUser;

        $app = $this->app;

        // trigger init event
        $this->app->event->dispatcher->notify($this->app->event->create($user, 'user:init', compact('app')));

        return $user;

    }

    public function getCurrent() {

        $user = parent::get();
        $app = $this->app;

        // trigger init event
        $this->app->event->dispatcher->notify($this->app->event->create($user, 'user:init', compact('app')));
        return $user;
    }

    public function save($user, $new = false) {

        $user->setParam('elements', $user->elements);

        $user->block = (int) $user->elements->get('status', 0) > 1;

        $result = $user->save() ? $user : false;

        

        if($result) {
            // trigger init event
            $this->app->event->dispatcher->notify($this->app->event->create($user, 'user:saved', compact('new')));
        }

        return $result;
    }

    public function getAccount($user) {
        $query = 'SELECT * FROM #__zoo_account_map WHERE child = '.$user->id.' AND type = "user"';

        $row = $this->app->database->queryObject($query);

        $account = $this->app->account->get($row->parent);

        return $account;

    }

    public function mapUserToAccount($user, $new = false) {
        if(!$account = $user->elements->get('account')) {
            return;
        }
        if($new) {
            $query = 'INSERT INTO #__zoo_account_user_map (parent, child) VALUES ('.$account.','.$user->id.')';
        } else {
            $query = 'UPDATE #__zoo_account_user_map SET parent = '.$account.' WHERE child = '.$user->id;
        }
        $this->app->database->query($query);
    }

    public function getAccountName($user) {
        $id = $user->elements->get('account');
        if($account = $this->app->account->get($id)) {
            return $account->name;
        }
        return;
        
    }


}