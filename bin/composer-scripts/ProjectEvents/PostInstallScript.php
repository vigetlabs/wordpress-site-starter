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
	 * @var array
	 */
	private static array $env = [];

	/**
	 * Perform the actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		// Load DDEV Environment variables.
		self::loadDDEVEnvironmentVars();

		if ( self::needsSetup() ) {

			// Download WordPress
			self::downloadWordPress();

			// Remove the core Twenty-X themes.
			self::deleteCoreThemes();
		}

		// Run ddev launch command.
		self::ddevLaunch();
	}

	/**
	 * Check if the setup is needed.
	 *
	 * @return bool
	 */
	private static function needsSetup(): bool {
		if ( file_exists( self::translatePath( './wp-load.php', true ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Read DDEV .env file and load the environment variables.
	 *
	 * @return void
	 */
	private static function loadDDEVEnvironmentVars(): void {
		$envPath = self::translatePath( './.ddev/.env', true );

		if ( ! file_exists( $envPath ) ) {
			self::writeError( 'DDEV .env file not found (' . $envPath . ')' );
			return;
		}

		self::writeInfo( 'Loading DDEV environment variables...' );

		self::$env = parse_ini_file( $envPath );

		self::writeInfo( 'DDEV environment variables loaded.' );
	}

	/**
	 * Download WordPress if it doesn't exist.
	 *
	 * @return void
	 */
	public static function downloadWordPress(): void {
		$wordpress_dir = self::translatePath( './', true );

		self::writeInfo( 'Downloading the last version of WordPress...' );

		$cmd = sprintf(
			'wp core download --path=%s --version=latest',
			escapeshellarg( $wordpress_dir )
		);

		self::runCommand( $cmd );

		self::writeInfo( 'WordPress Download complete.' );
	}

	/**
	 * Delete the core themes.
	 *
	 * @return void
	 */
	private static function deleteCoreThemes(): void {
		self::writeInfo( 'Deleting stock WordPress themes...' );

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

		self::writeInfo( 'Stock WordPress themes deleted.' );
	}

	/**
	 * Run the ddev launch command.
	 *
	 * @return void
	 */
	private static function ddevLaunch(): void {
		self::writeInfo( 'Launching DDEV...' );
		self::runCommand( 'ddev launch' );
	}
}
