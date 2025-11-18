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
	 * @var array
	 */
	private static array $repoPlugins = [
		'viget/viget-blocks-toolkit' => 'viget-blocks-toolkit',
		'viget/viget-form-blocks' => 'viget-form-blocks',
		'viget/viget-parts-kit' => 'viget-parts-kit',
		'viget/wp-sonny' => 'wp-sonny',
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
	 * Check if repo plugins exist locally (folder containing .git) and nullify their installation or update.
	 *
	 * @return void
	 */
	private static function checkRepoPlugins(): void {
		$installationManager = self::$composer->getInstallationManager();
		$protectedPackages = [];

		// Temporarily rename directories with .git folders
		foreach (self::$repoPlugins as $packageName => $folderName) {
			$package = self::$composer->getRepositoryManager()->getLocalRepository()->findPackage($packageName, '*');

			if ($package) {
				$installPath = $installationManager->getInstallPath($package);

				if (file_exists($installPath) && is_dir($installPath . '/.git')) {
					$tempPath = $installPath . '_git_backup_' . uniqid();

					// Skip if already renamed
					if (!is_dir($installPath) || is_dir($tempPath)) {
						continue;
					}

					self::writeInfo(sprintf('Protecting Git repository: %s', $packageName));
					rename($installPath, $tempPath);
					$protectedPackages[$packageName] = [
						'original' => $installPath,
						'temp' => $tempPath
					];
				}
			}
		}

		// Register post-event listeners if we have protected packages
		if (!empty($protectedPackages)) {
			// Store protected packages information in temporary file
			$tempFile = sys_get_temp_dir() . '/composer_protected_packages_' . md5(json_encode(array_keys($protectedPackages))) . '.json';
			file_put_contents($tempFile, json_encode($protectedPackages));

			register_shutdown_function(function() use ($tempFile) {
				if (file_exists($tempFile)) {
					self::restoreGitRepos($tempFile);
				}
			});

			// Listen for post events
			self::$event->getComposer()->getEventDispatcher()->addListener(
				'post-install-cmd',
				function() use ($tempFile) {
					self::restoreGitRepos($tempFile);
				},
				1000 // High priority to ensure this runs early
			);

			self::$event->getComposer()->getEventDispatcher()->addListener(
				'post-update-cmd',
				function() use ($tempFile) {
					self::restoreGitRepos($tempFile);
				},
				1000 // High priority to ensure this runs early
			);
		}
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
