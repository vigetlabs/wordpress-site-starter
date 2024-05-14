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
	 * Skip Database Option
	 */
	const SKIP_DB_OPT = 0;

	/**
	 * Install WordPress Option
	 */
	const INSTALL_WP_OPT = 1;

	/**
	 * Import Database Option
	 */
	const IMPORT_DB_OPT = 2;

	/**
	 * @var array
	 */
	private static array $env = [];

	/**
	 * @var array
	 */
	private static array $info = [];

	/**
	 * @var array
	 */
	private static array $deletePlugins = [
		'hello.php',
	];

	/**
	 * @var array
	 */
	private static array $activatePlugins = [
		'acf-blocks-toolkit' => [
			'name' => 'ACF Blocks Toolkit',
			'dependencies' => [
				'advanced-custom-fields-pro' => 'Advanced Custom Fields Pro',
			],
		],
		'svg-support' => 'SVG Support',
	];

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

			// Populate the database.
			self::populateDatabase();
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

		foreach ( self::$deletePlugins as $plugin ) {
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

		self::writeInfo( 'Stock WordPress plugins deleted.' );
	}

	/**
	 * Populate the database.
	 *
	 * @return void
	 */
	private static function populateDatabase(): void {
		$options = [
			self::INSTALL_WP_OPT => 'Install WordPress',
			self::IMPORT_DB_OPT  => 'Import Local Database File',
			// TODO: Pull from remote environment.
			self::SKIP_DB_OPT    => 'Skip',
		];

		$dbSource = intval( self::select( 'Select a database source:', $options, 'Install WordPress' ) );

		if ( ! $dbSource || self::SKIP_DB_OPT === $dbSource ) {
			return;
		}

		if ( self::IMPORT_DB_OPT === $dbSource ) {
			self::importDatabase();
			return;
		}

		// Run the WordPress Installation
		self::installWordPress();

		// Wait for WordPress to complete installation.
		self::wait( 2 );

		// Make sure WordPress was installed successfully.
		if ( ! self::isWordPressInstalled() ) {
			self::writeError( 'WordPress installation failed.' );
			return;
		}

		// Update the Site Description
		self::updateSiteDescription();

		// Activate our Custom Theme
		self::activateTheme();

		// Perform some post-install cleanup actions.
		self::installationCleanup();

		// Activate plugins.
		self::activatePlugins();

		// Show the success message.
		self::renderSuccessMessage();
	}

	/**
	 * Install WordPress
	 *
	 * @return void
	 */
	private static function installWordPress(): void {
		self::getSiteInfo();

		$command = sprintf(
			'wp core install --url=%s --title=%s --admin_user=viget --admin_email=%s --admin_password=%s',
			escapeshellarg( self::$info['url'] ),
			escapeshellarg( self::$info['title'] ),
			escapeshellarg( self::$info['email'] ),
			escapeshellarg( self::$info['password'] )
		);

		self::runCommand( $command );
	}

	/**
	 * Gather site info.
	 *
	 * @return void
	 */
	public static function getSiteInfo(): void {
		// Site Title.
		$defaultTitle = self::$env['PROJECT_NAME'] ?? 'WordPress Site';
		$title = ! empty( self::$info['title'] ) ? self::$info['title'] : $defaultTitle;
		self::$info['title'] = self::ask( 'What is the Site Title?*', $title );

		// Site Description.
		$name = self::$env['PROJECT_NAME'] ?? self::$info['title'];
		$description = ! empty( self::$info['description'] ) ? self::$info['description'] : sprintf( 'A custom WordPress Site for %s by Viget.', $name );
		self::$info['description'] = self::ask( 'What is the Site Description (tagline)?', $description );

		// Site URL.
		$defaultURL = ! empty( self::$env['PROJECT_SLUG'] ) ? 'https://' . self::$env['PROJECT_SLUG'] . '.ddev.site' : '';
		$url = ! empty( self::$info['url'] ) ? self::$info['url'] : $defaultURL;
		self::$info['url'] = self::ask( 'What is the URL?*', $url );

		// Admin Email.
		self::$info['email'] = self::$info['email'] ?? 'fed+wp@viget.com';
		self::$info['email'] = self::ask( 'What is the admin email address?*', self::$info['email'] );

		// Admin Password.
		self::$info['password'] = self::$info['password'] ?? self::generatePassword();
		self::$info['password'] = self::ask( 'Set the Admin User (viget) password:*', self::$info['password'] );

		// Check Required fields
		if ( empty( self::$info['title'] ) || empty( self::$info['url'] ) || empty( self::$info['email'] ) || empty( self::$info['password'] ) ) {
			self::writeError( 'Please complete all required fields.' );
			self::getSiteInfo();
		}

		// Summary
		$summary  = PHP_EOL . ' - Site Title: ' . self::$info['title'];
		$summary .= PHP_EOL . ' - Site Description: ' . ( self::$info['description'] ?: '[none]' );
		$summary .= PHP_EOL . ' - URL: ' . self::$info['url'];
		$summary .= PHP_EOL . ' - Admin Email: ' . self::$info['email'];
		$summary .= PHP_EOL . ' - Admin Password: ' . self::$info['password'];

		self::writeOutput( '<info>Summary:</info>' . $summary );

		if ( ! self::confirm( 'Does everything look right?' ) ) {
			self::getSiteInfo();
		}
	}

	/**
	 * Check if WordPress is installed.
	 *
	 * @return bool
	 */
	private static function isWordPressInstalled(): bool {
		return false !== shell_exec( 'wp core is-installed' );
	}

	/**
	 * Import database file
	 *
	 * @return void
	 */
	private static function importDatabase(): void {
		$databaseFile = self::ask( 'Please specify the path to the database file' );

		if ( ! $databaseFile ) {
			self::writeError( 'No database file provided.' );
			return;
		}

		$dbFilePath = self::translatePath( $databaseFile, true );

		if ( ! file_exists( $dbFilePath ) ) {
			self::writeError( 'Could not locate database file: ' . $dbFilePath );
			return;
		}

		self::writeInfo( 'Importing database...' );

		$cmd = sprintf(
			'wp db import %s',
			escapeshellarg( $databaseFile )
		);

		self::runCommand( $cmd );

		self::writeInfo( 'Database imported.' );
	}

	/**
	 * Update the site description.
	 *
	 * @return void
	 */
	private static function updateSiteDescription(): void {
		self::writeLine( 'Updating site description...' );

		$cmd = sprintf(
			'wp option update blogdescription "%s"',
			escapeshellarg( self::$info['description'] )
		);

		self::runCommand( $cmd );

		self::writeInfo( 'Site description updated.' );
	}

	/**
	 * Activate the custom theme.
	 *
	 * @return void
	 */
	private static function activateTheme(): void {
		$slug = self::$env['PROJECT_SLUG'] ?? basename( self::$env['VITE_PROJECT_DIR'] ) ?? '';
		if ( ! $slug || false === shell_exec( sprintf( 'wp theme is-installed %s', escapeshellarg( $slug ) ) ) ) {
			self::writeWarning( 'Skipping theme activation. Theme "' . $slug . '" not found.' );
			return;
		}

		self::writeInfo( 'Activating theme...' );

		$cmd = sprintf(
			'wp theme activate %s',
			escapeshellarg( $slug )
		);

		self::runCommand( $cmd );

		self::writeInfo( 'Theme activated.' );
	}

	/**
	 * Perform some post-install cleanup actions.
	 *
	 * @return void
	 */
	private static function installationCleanup(): void {
		self::writeComment( 'Performing post-install cleanup...' );

		// Homepage Actions.
		$homepage = [
			'wp post update 2 --post_title="Home" --post_name="home"',
			'wp option update show_on_front "page"',
			'wp option update page_on_front 2',
		];

		self::writeLine( 'Setting up homepage...' );
		foreach ( $homepage as $command ) {
			self::runCommand( $command );
		}

		$general = [
			'wp term update category 1 --name="General" --slug="general"',
			'wp rewrite structure "/%postname%/"',
			'wp option update rss_use_excerpt 1',
			'wp option update blog_public 0',
		];

		self::writeLine( 'Configuring general settings...' );
		foreach ( $general as $command ) {
			self::runCommand( $command );
		}

		$samples = [
			'wp comment delete 1 --force',
			'wp post delete 1 --force',
		];

		self::writeLine( 'Removing sample content...' );
		foreach ( $samples as $command ) {
			self::runCommand( $command );
		}

		$discussion = [
			'wp option update default_pingback_flag 0',
			'wp option update default_ping_status 0',
			'wp option update default_comment_status 0',
			'wp option update comment_registration 1',
			'wp option update comments_notify 0',
			'wp option update comment_moderation 1',
			'wp option update comment_max_links 0',
		];

		self::writeLine( 'Configuring discussion settings...' );
		foreach ( $discussion as $command ) {
			self::runCommand( $command );
		}

		self::writeInfo( 'Post-install cleanup complete.' );
	}

	/**
	 * Activate plugins.
	 *
	 * @return void
	 */
	private static function activatePlugins(): void {
		self::writeComment( 'Activating plugins...' );
		foreach ( self::$activatePlugins as $slug => $plugin ) {
			self::activatePlugin( $slug, $plugin );
		}

		self::writeInfo( 'Plugins activated.' );
	}

	/**
	 * Activate a plugin.
	 *
	 * @param string $slug
	 * @param string|array $plugin
	 *
	 * @return void
	 */
	private static function activatePlugin( string $slug, string|array $plugin ): void {
		if ( is_array( $plugin ) ) {
			foreach ( $plugin['dependencies'] as $depSlug => $depName ) {
				if ( false === shell_exec( sprintf( 'wp plugin is-installed %s', escapeshellarg( $depSlug ) ) ) ) {
					self::writeWarning( 'Skipping plugin activation. Dependency "' . $depSlug . '" not installed.' );
					return;
				}

				self::activatePlugin( $depSlug, $depName );
			}

			if ( false === shell_exec( sprintf( 'wp plugin is-installed %s', escapeshellarg( $slug ) ) ) ) {
				self::writeWarning( 'Skipping plugin activation. Plugin "' . $slug . '" not installed.' );
				return;
			}
			self::activatePlugin( $slug, $plugin['name'] );

			self::writeLine( $plugin['name'] . ' activated.' );
		} else {
			self::activatePlugin( $slug, $plugin );

			self::writeLine( $plugin . ' activated.' );
		}
	}

	/**
	 * Show the success message upon completion.
	 *
	 * @return void
	 */
	public static function renderSuccessMessage(): void {
		self::renderVigetLogo();

		$success = [
			'Congratulations!',
			'Project has been set up successfully!',
			'<fg=#F26D20>Important!</> Make a note of the Admin Password:',
			'<fg=#F26D20>' . self::$info['password'] . '</>',
		];

		$success = self::centeredText( $success, 2, '*', '#1296BB' );

		self::writeLine( $success );
	}
}
