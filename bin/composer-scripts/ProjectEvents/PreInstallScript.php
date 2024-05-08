<?php
/**
 * Perform some pre-install actions with Composer.
 */

namespace Viget\ComposerScripts\ProjectEvents;

use Composer\Script\Event;
use Viget\ComposerScripts\ComposerScript;

/**
 * Pre Install Composer Script
 */
class PreInstallScript extends ComposerScript {

	/**
	 * Perform the actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		if ( ! PostCreateProjectScript::needsSetup() ) {
			return;
		}

		PostCreateProjectScript::execute( $event );

	}
}
