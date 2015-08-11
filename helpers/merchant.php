<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class MerchantHelper extends AppHelper {

    protected $merchant;
    protected $testMode = false;

    
    public function anet() {
        $this->testMode = (bool) $this->app->zoo->getApplication()->getParams()->get('global.store.testing');
        if($this->testMode) {
            define("AUTHORIZENET_API_LOGIN_ID", "9f4Wf2E7");
            define("AUTHORIZENET_TRANSACTION_KEY", "65mq3Mn8C422BMk7");
            define("AUTHORIZENET_SANDBOX", true);
        } else {
            define("AUTHORIZENET_API_LOGIN_ID", "4xYXc62C6Uc");
            define("AUTHORIZENET_TRANSACTION_KEY", "87zq6EPH4V9swa4z");
            define("AUTHORIZENET_SANDBOX", false);
        }

        $this->merchant['anet'] = new AuthorizeNetAIM;

    }

    public function testMode() {
        return $this->testMode;
    }
    
    public function __get($name) {
        $this->$name();
        return $this->merchant[$name];
    }
    //put your code here
}
