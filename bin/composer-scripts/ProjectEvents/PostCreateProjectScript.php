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

		// Gather project info.
		self::getProjectInfo();

		// Perform project string replacements
		self::updateProjectFiles();

		// Require ACF if auth.json file is present.
		self::maybeRequireACF();

		// Modify the description in the composer.json file.
		self::updateComposerDescription();
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
	 * Gather project info.
	 *
	 * @return void
	 */
	public static function getProjectInfo(): void {
		// Project Name.
		$name = ! empty( self::$info['name'] ) ? self::$info['name'] : 'My Project';
		self::$info['name'] = self::ask( 'What is the name of your project?', $name );

		// Project Slug.
		self::$info['slug'] = self::slugify( self::$info['name'] );
		self::$info['slug'] = self::ask( 'Do you want to use a custom project slug?', self::$info['slug'] );

		// Text Domain.
		self::$info['text_domain'] = self::$info['slug'];
		self::$info['text_domain'] = self::ask( 'Should the text domain match the project slug?', self::$info['text_domain'] );

		// Project Package.
		self::$info['package'] = str_replace( ' ', '', self::$info['name'] );
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

		// Change the theme directory name.
		rename( $default_theme_dir, $theme_dir );

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
			return;
		}

		$acf_package   = 'wpengine/advanced-custom-fields-pro';
		$composer_path = self::translatePath( 'composer.json' );
		$composer_json = file_get_contents( $composer_path );
		$composer_data = json_decode( $composer_json, true );

		if ( ! empty( $composer_data['require'][ $acf_package ] ) ){
			return;
		}

		$composer_data['require'][ $acf_package ] = "*";

		file_put_contents(
			$composer_path,
			json_encode( $composer_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
		);
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

		$search  = self::$event->getComposer()->getPackage()->getDescription();
		$replace = sprintf( 'A custom WordPress Site for %s by Viget.', self::$info['name'] );

		self::searchReplaceFile( $search, $replace, 'composer.json' );
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
			'.ddev/config.yml',
			'composer.json',
			$theme_dir . '/composer.json',
			$theme_dir . '/package.json',
			$theme_dir . '/package-lock.json',
			'.phpcs.xml',
			$theme_dir . '/.phpcs.xml',
			$theme_dir . '/readme.txt',
			'README.md',
			$theme_dir . '/README.md',
			$theme_dir . '/style.css',
			$theme_dir . '/vite.config.js',
		];

		$theme_php_files  = glob( $theme_dir . '/**/*.php' );
		$theme_html_files = glob( $theme_dir . '/**/*.html' );

		return array_merge( $files, $theme_php_files, $theme_html_files );
	}
}
