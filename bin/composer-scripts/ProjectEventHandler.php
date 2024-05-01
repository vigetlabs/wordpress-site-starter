<?php
/**
 * Project Event Handler for Composer
 */

namespace Viget\ComposerScripts;

use Composer\Script\Event;
use Viget\ComposerScripts\ProjectEvents\PostCreateProjectScript;
use Viget\ComposerScripts\ProjectEvents\PostInstallScript;
use Viget\ComposerScripts\ProjectEvents\PreInstallScript;

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
	 */
	public static function preInstall( Event $event ): void {
		PreInstallScript::execute( $event );
	}

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
