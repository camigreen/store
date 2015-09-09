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
class UserProfileHelper extends AppHelper {

    protected $_profiles = array();

    public function getName() {
        return 'userprofile';
    }

    public function get($id = null) {
        if (empty($id)) {
            $id = $this->app->user->get()->id;
        }
        $table = $this->app->table->userprofile;
        if (!in_array($id, $this->_profiles)) {
            $this->_profiles[$id] = $table->get($id);
        }
        return $this->_profiles[$id];
    }

    /**
     * Check if a user can access a resource
     *
     * @param JUser $user The user to check
     * @param int $access The access level to check against
     *
     * @return boolean If the user have the rights to access that level
     *
     * @since 1.0.0
     */
    public function canAccess($user = null, $access = 0) {

        if (is_null($user)) {
            $user = $this->get();
        }

        return in_array($access, $user->getAuthorisedViewLevels());

    }

    public function canCreateOrders($user = null, $asset_id = 0) {
        if(empty($user)) {
            $user = $this->app->user->get();
        }
        return $this->app->user->isAdmin($user, $asset_id) || $this->app->user->authorise($user, 'order.create', $asset_id);

    }
    public function canEditOrders($user = null, $asset_id = 0) {
        if(empty($user)) {
            $user = $this->app->user->get();
        }
        return $this->app->user->isAdmin($user, $asset_id) || $this->app->user->authorise($user, 'order.edit', $asset_id);

    }
    public function canDeleteOrders($user = null, $asset_id = 0) {
        if(empty($user)) {
            $user = $this->app->user->get();
        }
        return $this->app->user->isAdmin($user, $asset_id) || $this->app->user->authorise($user, 'order.delete', $asset_id);

    }

}