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

		// Download WordPress
		self::maybeDownloadWordPress();

		// Run composer install in the theme directory.
		self::themeComposerInstall();
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

	/**
	 * Run composer install in the theme directory.
	 *
	 * @return void
	 */
	private static function themeComposerInstall(): void {
		if ( empty( self::$env['VITE_PROJECT_DIR'] ) ) {
			self::writeError( 'Missing VITE_PROJECT_DIR environment variable.' );
			return;
		}

		$themeDir = self::translatePath( self::$env['VITE_PROJECT_DIR'], true );

		if ( ! is_dir( $themeDir ) ) {
			self::writeError( 'Theme directory not found: ' . $themeDir );
			return;
		}

		self::writeInfo( 'Running composer install in the theme directory...' );

		$cmd = sprintf(
			'cd %s && ddev composer install',
			escapeshellarg( $themeDir )
		);

		self::runCommand( $cmd );
	}
}
