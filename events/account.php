<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * Deals with application events.
 *
 * @package Component.Events
 */
class AccountEvent {

	/**
	 * When an application is loaded on the frontend,
	 * load the language files from the app folder too
	 *
	 * @param  AppEvent 	$event The event triggered
	 */
	public static function init($event) {

		$account = $event->getSubject();
        $app         = $account->app;
        echo 'Account Initialized: '.$account->name;

        
        if (is_string($account->params) || is_null($account->params)) {
            // decorate data as this
            $account->params = $app->parameter->create($account->params);
        }

        include($app->path->path('classes:/accounts/config.php'));
        
        foreach ($config['types'][$account->type] as $key => $value) {
            if(!$account->params->get($key)) {
                $account->params->set($key, $value);
            }
        }

	}

	/**
	 * Placeholder for the saved event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$account = $event->getSubject();
		$new = $event['new'];

	}

	/**
	 * Placeholder for the deleted event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function deleted($event) {

		$account = $event->getSubject();

	}

	/**
	 * Placeholder for the installed event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function installed($event) {

		$account = $event->getSubject();
		$update = $event['update'];

	}

	/**
	 * Placeholder for the addmenuitems event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function addmenuitems($event) {

		$account = $event->getSubject();

		// Tab object
		$tab = $event['tab'];

		// add child

		// return the tab object
		$event['tab'] = $tab;
	}

}