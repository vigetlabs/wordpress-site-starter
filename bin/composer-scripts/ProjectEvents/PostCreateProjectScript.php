<?php
/**
 * Perform some post-create-project actions with Composer.
 */

namespace Viget\ComposerScripts\ProjectEvents;

use Composer\Script\Event;
use Viget\ComposerScripts\ComposerScript;

/**
 * Post Create Project Composer Script
 */
class PostCreateProjectScript extends ComposerScript {

	/**
	 * Perform actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		self::replaceComposerFile();
		self::removeThisScript();
	}

	/**
	 * Replace the composer.json file with the composer.json.dist file.
	 *
	 * @return void
	 */
	public static function replaceComposerFile(): void {
		$composer_file    = self::translatePath( 'composer.json' );
		$replacement_file = self::translatePath( 'composer.json.dist' );

		if ( ! file_exists( $replacement_file ) ) {
			return;
		}

		copy( $replacement_file, $composer_file );
		unlink( $replacement_file );
	}

	/**
	 * Self destruct.
	 *
	 * @return void
	 */
	public static function removeThisScript(): void {
		$script_file = self::translatePath( __FILE__ );
		unlink( $script_file );
	}
}
