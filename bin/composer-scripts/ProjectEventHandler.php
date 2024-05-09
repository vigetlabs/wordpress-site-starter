<?php
/**
 * Project Event Handler for Composer
 */

namespace Viget\ComposerScripts;

use Composer\Script\Event;
use Viget\ComposerScripts\ProjectEvents\PostCreateProjectScript;
use Viget\ComposerScripts\ProjectEvents\PostInstallScript;

/**
 * Handle Project Events
 */
class ProjectEventHandler {

	/**
	 * Post create project event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function postCreateProject( Event $event ): void {
		PostCreateProjectScript::execute( $event );
	}

	/**
	 * Post install event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function postInstall( Event $event ): void {
		PostInstallScript::execute( $event );
	}
}
