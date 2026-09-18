<?php
/**
 * Perform some pre-install/update checks with Composer.
 */

namespace Viget\ComposerScripts\ProjectEvents;

use Composer\Script\Event;
use Viget\ComposerScripts\ComposerScript;

/**
 * Pre Install/Update Composer Scripts
 */
class PreScripts extends ComposerScript {

	/**
	 * Directories scanned for locally cloned packages.
	 *
	 * @var array
	 */
	private static array $scanDirs = [
		'wp-content/plugins',
		'wp-content/mu-plugins',
		'wp-content/themes',
	];

	/**
	 * Initialize the script.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function preInstall( Event $event ): void {
		self::setEvent( $event );

		// Do not run on deployment.
		if ( ! $event->isDevMode() ) {
			return;
		}

		// Check for repo plugins.
		self::checkRepoPlugins();
	}

	/**
	 * Pre update event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function preUpdate( Event $event ): void {
		self::setEvent( $event );

		// Do not run on deployment.
		if ( ! $event->isDevMode() ) {
			return;
		}

		// Check for repo plugins.
		self::checkRepoPlugins();
	}

	/**
	 * Move locally cloned repos out of the way so Composer can't overwrite them.
	 *
	 * @return void
	 */
	private static function checkRepoPlugins(): void {
		$protectedPackages = [];

		foreach ( self::findGitRepos() as $label => $installPath ) {
			$tempPath = $installPath . '_git_backup_' . uniqid();

			if ( is_dir( $tempPath ) ) {
				continue;
			}

			self::writeInfo( sprintf( 'Protecting Git repository: %s', $label ) );

			rename( $installPath, $tempPath );

			$protectedPackages[ $label ] = [
				'original' => $installPath,
				'temp'     => $tempPath,
			];
		}

		if ( empty( $protectedPackages ) ) {
			return;
		}

		// Store protected packages information in temporary file
		$tempFile = sys_get_temp_dir() . '/composer_protected_packages_' . md5( json_encode( array_keys( $protectedPackages ) ) ) . '.json';
		file_put_contents( $tempFile, json_encode( $protectedPackages ) );

		register_shutdown_function( function () use ( $tempFile ) {
			if ( file_exists( $tempFile ) ) {
				self::restoreGitRepos( $tempFile );
			}
		} );

		// Listen for post events
		foreach ( [ 'post-install-cmd', 'post-update-cmd' ] as $eventName ) {
			self::$event->getComposer()->getEventDispatcher()->addListener(
				$eventName,
				function () use ( $tempFile ) {
					self::restoreGitRepos( $tempFile );
				},
				1000 // High priority to ensure this runs early
			);
		}
	}

	/**
	 * Find plugin, mu-plugin and theme directories that are Git clones.
	 *
	 * The package running Composer is skipped - moving it would break the install.
	 *
	 * @return array Label => absolute path.
	 */
	private static function findGitRepos(): array {
		$repos  = [];
		$self   = self::getProjectFolder();

		foreach ( self::$scanDirs as $scanDir ) {
			$dirs = glob( self::translatePath( $scanDir ) . '/*', GLOB_ONLYDIR );

			foreach ( $dirs as $dir ) {
				if ( ! is_dir( $dir . '/.git' ) || realpath( $dir ) === $self ) {
					continue;
				}

				$repos[ basename( $scanDir ) . '/' . basename( $dir ) ] = $dir;
			}
		}

		return $repos;
	}

	/**
	 * Restore Git repositories after Composer finishes.
	 *
	 * @param string $tempFile Path to temporary file with backup information
	 * @return void
	 */
	private static function restoreGitRepos(string $tempFile): void {
		if (!file_exists($tempFile)) {
			return;
		}

		$protectedPackages = json_decode(file_get_contents($tempFile), true);
		unlink($tempFile); // Clean up

		if (empty($protectedPackages)) {
			return;
		}

		foreach ($protectedPackages as $packageName => $paths) {
			$originalPath = $paths['original'];
			$tempPath = $paths['temp'];

			if (is_dir($tempPath)) {
				// Remove the newly installed version if it exists
				if (is_dir($originalPath)) {
					self::deleteDirectory($originalPath);
				}

				// Restore from temp location
				rename($tempPath, $originalPath);
				self::writeInfo(sprintf('Restored Git repository: %s', $packageName));
			}
		}
	}
}
