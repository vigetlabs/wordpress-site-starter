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

		self::wait();

		if ( self::needsSetup() ) {

			// Download WordPress
			self::downloadWordPress();

			self::wait( 2 );

			// Remove the core Twenty-X themes.
			self::deleteCoreThemes();

			// Remove Hello Dolly.
			self::deleteCorePlugins();
		}
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
	 * Download WordPress
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

		if ( self::needsSetup() ) {
			self::writeError( 'WordPress download seems to have failed. Verify you currently have internet access and try again.' );
			exit( 1 );
		}

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
			$themeDir = self::translatePath( 'wp-content/themes/' . $theme );

			if ( ! is_dir( $themeDir ) ) {
				continue;
			}

			self::deleteDirectory( $themeDir );
		}

		self::writeInfo( 'Stock WordPress themes deleted.' );
	}

	/**
	 * Delete the core plugins.
	 *
	 * @return void
	 */
	private static function deleteCorePlugins(): void {
		self::writeInfo( 'Deleting stock WordPress plugins...' );

		$plugins = [
			'hello.php',
		];

		foreach ( $plugins as $plugin ) {
			$pluginFile = self::translatePath( 'wp-content/plugins/' . $plugin );

			if ( file_exists( $pluginFile ) ) {
				unlink( $pluginFile );
				continue;
			}

			if ( str_ends_with( $pluginFile, '.php' ) ) {
				$pluginDir = str_replace( basename( $pluginFile ), '', $pluginFile );
			} else {
				$pluginDir = $pluginFile;
			}

			if ( ! is_dir( $pluginDir ) ) {
				continue;
			}

			self::deleteDirectory( $pluginDir );
		}

		self::writeInfo( 'Stock WordPress themes deleted.' );
	}
}
