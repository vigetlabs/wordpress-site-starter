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
	 * @var array
	 */
	private static array $info = [];

	/**
	 * @var string
	 */
	private static string $default_project_slug = 'wordpress-site-starter';

	/**
	 * @var string
	 */
	private static string $default_project_name = 'WP Site Starter';

	/**
	 * @var string
	 */
	private static string $default_alt_project_name = 'WordPress Site Starter';

	/**
	 * @var string
	 */
	private static string $default_host_name = 'wpstarter';

	/**
	 * @var string
	 */
	private static string $default_theme_name = 'WP Starter';

	/**
	 * @var string
	 */
	private static string $default_theme_slug = 'wp-starter';

	/**
	 * @var string
	 */
	private static string $default_package_name = 'WPStarter';

	/**
	 * @var string
	 */
	private static string $default_function_prefix = 'wpstarter_';

	/**
	 * @var string
	 */
	private static string $default_text_domain = 'wp-starter';

	/**
	 * Perform actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		if ( ! self::needsSetup() ) {
			return;
		}

		if ( ! self::meetsRequirements() ) {
			self::writeWarning( 'Requirements not met. Exiting.' );
			return;
		}

		// Gather project info.
		self::getProjectInfo();

		// Modify the description in the composer.json file.
		self::updateComposerDescription();

		// Modify the description in the composer.json file.
		self::updateRemoveUnnecessaryDependencies();

		// Perform project string replacements
		self::updateProjectFiles();

		// Require ACF if auth.json file is present.
		self::maybeRequireACF();

		// Self Destruct.
		self::destruct();
	}

	/**
	 * Check to see if we should run setup.
	 *
	 * @return bool
	 */
	public static function needsSetup(): bool {
		$package = self::$event->getComposer()->getPackage()->getName();

		if ( ! str_contains( $package, self::$default_project_slug ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the requirements are met.
	 *
	 * @return bool
	 */
	private static function meetsRequirements(): bool {
		// Check if DDEV is installed
		if ( ! shell_exec( 'which ddev' ) ) {
			self::writeError( 'DDEV is required for this script. Please install DDEV and try again.' );
			return false;
		}

		return true;
	}

	/**
	 * Gather project info.
	 *
	 * @return void
	 */
	public static function getProjectInfo(): void {
		// Project Name.
		$default_name = ucwords( str_replace( [ '-', '_' ], ' ', basename( getcwd() ) ) );
		$name = ! empty( self::$info['name'] ) ? self::$info['name'] : $default_name;
		self::$info['name'] = self::ask( 'What is the name of your project?', $name );

		// Project Slug.
		self::$info['slug'] = self::slugify( self::$info['name'] );
		self::$info['slug'] = self::ask( 'Do you want to use a custom project slug?', self::$info['slug'] );

		// Text Domain.
		self::$info['text_domain'] = self::$info['slug'];
		self::$info['text_domain'] = self::ask( 'Should the text domain match the project slug?', self::$info['text_domain'] );

		// Project Package.
		self::$info['package'] = str_replace( ' ', '', ucwords( self::$info['name'] ) );
		self::$info['package'] = self::ask( 'Do you want to customize the package name?', self::$info['package'] );

		// Function Prefix.
		self::$info['function'] = str_replace( '-', '_', self::$info['slug'] ) . '_';
		self::$info['function'] = self::ask( 'Do you want to customize the function prefix?', self::$info['function'] );

		// Summary
		$summary  = PHP_EOL . ' - Name: ' . self::$info['name'];
		$summary .= PHP_EOL . ' - Slug: ' . self::$info['slug'];
		$summary .= PHP_EOL . ' - Text Domain: ' . self::$info['text_domain'];
		$summary .= PHP_EOL . ' - Package: ' . self::$info['package'];
		$summary .= PHP_EOL . ' - Function Prefix: ' . self::$info['function'];

		self::writeOutput( '<info>Summary:</info>' . $summary );

		if ( ! self::confirm( 'Does everything look good?' ) ) {
			self::getProjectInfo();
		}
	}

	/**
	 * Slugify some text.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	private static function slugify( string $text ): string {
		$separator = '-';

		// replace non letter or digits by separator
		$text = preg_replace( '~[^\pL\d]+~u', $separator, $text );

		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );

		// trim
		$text = trim( $text, $separator );

		// remove duplicate separator
		$text = preg_replace( '~-+~', $separator, $text );

		// lowercase
		$text = strtolower( $text );

		if ( empty( $text ) ) {
			return '';
		}

		return $text;
	}

	/**
	 * Change wordpress-starter-project to match new project
	 *
	 * @return void
	 */
	public static function updateProjectFiles(): void {
		if ( empty( self::$info['slug'] ) ) {
			self::writeError( 'Missing project slug.' );
			return;
		}

		$default_theme_dir = self::translatePath( 'wp-content/themes/' . self::$default_theme_slug );
		$theme_dir         = self::translatePath( 'wp-content/themes/' . self::$info['slug'] );

		if ( ! is_dir( $default_theme_dir ) ) {
			self::writeError( 'Missing theme directory.' );
			return;
		}

		self::writeInfo( 'Changing theme directory name...' );
		self::writeComment( 'Theme Directory: ' . $theme_dir );

		// Change the theme directory name.
		if ( ! rename( $default_theme_dir, $theme_dir ) ) {
			self::writeError( 'Failed to rename theme directory.' );
			return;
		}

		self::writeInfo( 'Theme directory name changed.' );

		$files = self::getFilesToChange( $theme_dir );

		$search = [
			[
				self::$default_function_prefix,
			],
			[
				'\'' . self::$default_text_domain . '\'',
				'Text Domain: ' . self::$default_text_domain,
			],
			[
				self::$default_project_slug,
				self::$default_host_name,
				self::$default_theme_slug,
			],
			[
				self::$default_project_name,
				self::$default_alt_project_name,
				self::$default_theme_name,
			],
			[
				self::$default_package_name,
			],
		];

		$replace = [
			self::$info['function'], // Function prefix.
			[
				'\'' . self::$info['text_domain'] . '\'', // Text Domain.
				'Text Domain: ' . self::$info['text_domain'],
			],
			self::$info['slug'], // Project Slug.
			self::$info['name'], // Project Name.
			self::$info['package'], // Package name.
		];

		self::writeInfo( 'Performing string replacements...' );

		foreach ( $files as $file ) {
			foreach ( $search as $index => $group ) {
				self::searchReplaceFile( $group, $replace[ $index ], $file );
			}
		}

		self::writeInfo( 'All set!' );
	}

	/**
	 * Require ACF if auth.json file is present.
	 *
	 * @return void
	 */
	public static function maybeRequireACF(): void {
		$auth_path = self::translatePath( 'auth.json' );

		if ( ! file_exists( $auth_path ) ) {
			self::writeWarning( 'auth.json file not found. Skipping ACF requirement.' );
			return;
		}

		$acfPackage   = 'wpengine/advanced-custom-fields-pro';
		$composerData = self::getComposerData();

		if ( ! empty( $composerData['require'][ $acfPackage ] ) ){
			return;
		}

		$composerData['require'][ $acfPackage ] = "*";

		self::updateComposerData( $composerData );
	}

	/**
	 * Modify the composer.json project description
	 *
	 * @return void
	 */
	public static function updateComposerDescription(): void {
		if ( empty( self::$info['name'] ) ) {
			self::writeError( 'Missing project name.' );
			return;
		}

		$composerData = self::getComposerData();
		$composerData['description'] = sprintf( 'A custom WordPress Site for %s by Viget.', self::$info['name'] );
		self::updateComposerData( $composerData );
	}

	/**
	 * Modify the composer.json to remove unnecessary dependencies
	 *
	 * @return void
	 */
	public static function updateRemoveUnnecessaryDependencies(): void {
		if ( empty( self::$info['name'] ) ) {
			self::writeError( 'Missing project name.' );
			return;
		}

		$composerData = self::getComposerData();

		// Remove post-create-project-cmd
		unset( $composerData['post-create-project-cmd'] );

		// Remove pre-install-cmd
		unset( $composerData['pre-install-cmd'] );

		// Remove Composer
		unset( $composerData['require-dev']['composer/composer'] );

		// Remove Symfony Console
		unset( $composerData['require-dev']['symfony/console'] );

		self::updateComposerData( $composerData );
	}

	/**
	 * Get all the files that need to be updated.
	 *
	 * @param string $theme_dir
	 *
	 * @return array
	 */
	private static function getFilesToChange( string $theme_dir ): array {
		$files = [
			self::translatePath( '.ddev/config.yaml' ),
			self::translatePath( 'composer.json' ),
			$theme_dir . '/composer.json',
			$theme_dir . '/package.json',
			$theme_dir . '/package-lock.json',
			self::translatePath( '.phpcs.xml' ),
			$theme_dir . '/.phpcs.xml',
			$theme_dir . '/readme.txt',
			self::translatePath( 'README.md' ),
			$theme_dir . '/README.md',
			$theme_dir . '/style.css',
			$theme_dir . '/vite.config.js',
		];

		$theme_php_files  = glob( $theme_dir . '/**/*.php' );
		$theme_html_files = glob( $theme_dir . '/**/*.html' );

		return array_merge( $files, $theme_php_files, $theme_html_files );
	}

	/**
	 * Self Destruct
	 *
	 * @return void
	 */
	private static function destruct(): void {
		// Remove PostCreateProjectScript file
		$createProject = self::translatePath( 'bin/composer-scripts/ProjectEvents/PostCreateProjectScript.php' );
		unlink( $createProject );

		// Remove PreInstallScript file
		$preInstall = self::translatePath( 'bin/composer-scripts/ProjectEvents/PreInstallScript.php' );
		unlink( $preInstall );
	}
}
