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
class UserEvent {

	/**
	 * When an application is loaded on the frontend,
	 * load the language files from the app folder too
	 *
	 * @param  AppEvent 	$event The event triggered
	 */
	public static function init($event) {

		$user = $event->getSubject();
        $app         = $event['app'];

        $user->app = $app;
        $user->elements = $app->parameter->create($user->getParam('elements'));
        $user->status = $user->elements->get('status', 0);
        $user->type = $user->elements->get('type','employee');

	}

	/**
	 * Placeholder for the saved event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$user = $event->getSubject();
		$new = (bool)$event['new'];
		$app = $user->app;
		$app->suser->mapUserToAccount($user, $new);



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