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
class UserProfileEvent {

	/**
	 * When an application is loaded on the frontend,
	 * load the language files from the app folder too
	 *
	 * @param  AppEvent 	$event The event triggered
	 */
	public static function init($event) {

		$userprofile = $event->getSubject();
        $app         = $userprofile->app;

        if(!$event['new']) {
	        $user = $app->user->get($userprofile->id);

	        foreach(get_object_vars($user) as $key => $value) {
	        	if(property_exists($userprofile, $key)) {
	        		$userprofile->$key = $value;
	        	}
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

}