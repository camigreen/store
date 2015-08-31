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
class UserProfileHelper extends UserAppHelper {

    public function getName() {
        return 'userprofile';
    }

    public function canCreateOrders($user = null, $asset_id = 0) {
        if(is_null($user)) {
            $user = $this->_get();
        }
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'order.create', $asset_id);

    }
    public function canEditOrders($user = null, $asset_id = 0) {
        if(is_null($user)) {
            $user = $this->_get();
        } 
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'order.edit', $asset_id);

    }
    public function canDeleteOrders($user = null, $asset_id = 0) {
        if(is_null($user)) {
            $user = $this->_get();
        } 
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'order.delete', $asset_id);

    }

}