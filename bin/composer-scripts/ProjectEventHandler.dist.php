<?php
/**
 * Project Event Handler for Composer
 */

namespace Viget\ComposerScripts;

use Composer\Script\Event;
use Viget\ComposerScripts\ProjectEvents\PostInstallScript;

/**
 * Handle Project Events
 */
class ProjectEventHandler {

	/**
	 * Post install event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function postInstall( Event $event ): void {
		PostInstallScript::execute( $event );
	}
}
