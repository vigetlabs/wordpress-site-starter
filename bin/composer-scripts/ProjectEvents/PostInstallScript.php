<?php
/**
 * Perform some post-install actions with Composer.
 */

namespace Viget\ComposerScripts\ProjectEvents;

use Composer\Script\Event;
use Viget\ComposerScripts\ComposerScript;

/**
 * Post Install Composer Script
 */
class PostInstallScript extends ComposerScript {

	/**
	 * Perform the actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		// Download WordPress
		self::maybeDownloadWordPress();
	}

	/**
	 * Download WordPress if it doesn't exist.
	 *
	 * @return void
	 */
	public static function maybeDownloadWordPress(): void {
		if ( file_exists( self::translatePath( './wp-load.php', true ) ) ) {
			return;
		}

		$wordpress_dir = self::translatePath( './', true );

		self::writeInfo( 'Downloading the last version of WordPress...' );

		$cmd = sprintf(
			'ddev wp core download --path=%s --version=latest',
			escapeshellarg( $wordpress_dir )
		);

		self::runCommand( $cmd );

		self::writeInfo( 'Deleting stock WordPress themes...' );

		// Remove the core Twenty-X themes.
		self::deleteCoreThemes();
	}

	/**
	 * Delete the core themes.
	 *
	 * @return void
	 */
	private static function deleteCoreThemes(): void {
		$themes = [
			'twentytwenty',
			'twentytwentyone',
			'twentytwentytwo',
			'twentytwentythree',
			'twentytwentyfour',
		];

		foreach ( $themes as $theme ) {
			$theme_dir = self::translatePath( 'wp-content/themes/' . $theme );

			if ( ! is_dir( $theme_dir ) ) {
				continue;
			}

			self::deleteDirectory( $theme_dir );
		}
	}
}
