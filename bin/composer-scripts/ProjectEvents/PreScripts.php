<?php
/**
 * Perform some pre-install/update checks with Composer.
 */

namespace Viget\ComposerScripts\ProjectEvents;

use Composer\Script\Event;
use Viget\ComposerScripts\ComposerScript;

/**
 * Pre Install/Update Composer Script
 */
class PreScripts extends ComposerScript {

	/**
	 * @var array
	 */
	private static array $repoPlugins = [
		'viget/viget-blocks-toolkit' => 'viget-blocks-toolkit',
		'viget/viget-form-blocks' => 'viget-form-blocks',
		'viget/viget-parts-kit' => 'viget-parts-kit',
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
	 * Initialize the script.
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
	 * Check if repo plugins exist locally and skip their installation if they do.
	 *
	 * @return void
	 */
	private static function checkRepoPlugins(): void {
		foreach ( self::$repoPlugins as $packageName => $pluginDir ) {
			$pluginGitDir = self::translatePath( 'wp-content/plugins/' . $pluginDir . '/.git', true );
			if ( is_dir( $pluginGitDir ) ) {
				self::writeInfo( sprintf( 'Skipping installation of %s: directory containing repository exists.', $packageName ) );

				// Ensure the package is marked as "installed".
				self::$event->getComposer()->getPackage()->setReplaces( array_merge(
					self::$event->getComposer()->getPackage()->getReplaces(),
					[
						$packageName => '*',
					]
				) );
			}
		}
	}
}
