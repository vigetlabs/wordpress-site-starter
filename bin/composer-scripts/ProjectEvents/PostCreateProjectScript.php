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
	 * @var array
	 */
	private static array $defaults = [
		'project-slug'     => 'wordpress-site-starter',
		'project-name'     => 'WP Site Starter',
		'alt-project-name' => 'WordPress Site Starter',
		'host-name'        => 'wpstarter',
		'theme-name'       => 'WP Starter',
		'theme-slug'       => 'wp-starter',
		'package-name'     => 'WPStarter',
		'function-prefix'  => 'wpstarter_',
		'text-domain'      => 'wp-starter',
	];

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

		// Perform project string replacements
		self::updateProjectFiles();

		// Modify the description in the composer.json file.
		self::updateComposerDescription();

		// Swap README files
		self::swapReadmeFiles();

		// Require ACF if auth.json file is present.
		self::maybeRequireACF();

		// Remove dev-only packages file.
		self::removePackagesFile();

		// Remove site-starter composer file
		self::removeRootComposer();

		// Self Destruct.
		self::destruct();

		self::writeInfo( 'All set!' );
	}

	/**
	 * Check to see if we should run setup.
	 *
	 * @return bool
	 */
	public static function needsSetup(): bool {
		$package = self::$event->getComposer()->getPackage()->getName();

		if ( ! str_contains( $package, self::$defaults['project-slug'] ) ) {
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
		self::writeInfo( 'Checking requirements...' );

		// Check if DDEV is installed
		if ( ! shell_exec( 'which ddev' ) ) {
			self::writeError( 'DDEV is required for this script. Please install DDEV and try again.' );
			return false;
		}

		self::writeInfo( 'Requirement Passed!' );

		return true;
	}

	/**
	 * Gather project info.
	 *
	 * @return void
	 */
	public static function getProjectInfo(): void {
		// Project Name.
		$defaultName = ucwords( str_replace( [ '-', '_' ], ' ', basename( getcwd() ) ) );
		$name = ! empty( self::$info['name'] ) ? self::$info['name'] : $defaultName;
		self::$info['name'] = self::ask( 'What is the name of your project?', $name );

		// Project Slug.
		self::$info['slug'] = self::slugify( self::$info['name'] );
		self::$info['slug'] = self::ask( 'Do you want to use a custom project slug?', self::$info['slug'] );

		// Text Domain.
		self::$info['text-domain'] = self::$info['slug'];
		self::$info['text-domain'] = self::ask( 'Should the text domain match the project slug?', self::$info['text-domain'] );

		// Project Package.
		self::$info['package'] = str_replace( ' ', '', ucwords( self::$info['name'] ) );
		self::$info['package'] = self::ask( 'Do you want to customize the package name?', self::$info['package'] );

		// Function Prefix.
		self::$info['function'] = str_replace( '-', '_', self::$info['slug'] ) . '_';
		self::$info['function'] = self::ask( 'Do you want to customize the function prefix?', self::$info['function'] );

		// Summary
		$summary  = PHP_EOL . ' - Name: ' . self::$info['name'];
		$summary .= PHP_EOL . ' - Slug: ' . self::$info['slug'];
		$summary .= PHP_EOL . ' - Text Domain: ' . self::$info['text-domain'];
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
	 * Swap the README files.
	 *
	 * @return void
	 */
	private static function swapReadmeFiles(): void {
		self::writeInfo( 'Swapping README files...' );

		$readmePath    = self::translatePath( 'README.md' );
		$projectReadme = self::translatePath( 'README.dist.md' );

		if ( ! file_exists( $readmePath ) ||  ! file_exists( $projectReadme ) ) {
			self::writeWarning( 'Missing one or more README files - Skipping README swap.' );
			return;
		}

		// Swap the README files.
		unlink( $readmePath );
		rename( $projectReadme, $readmePath );

		self::writeInfo( 'README files swapped.' );
	}

	/**
	 * Change wordpress-starter-project to match new project
	 *
	 * @return void
	 */
	public static function updateProjectFiles(): void {
		self::writeInfo( 'Updating project files...' );

		if ( empty( self::$info['slug'] ) ) {
			self::writeError( 'Missing project slug.' );
			return;
		}

		$defaultThemeDir = self::translatePath( 'wp-content/themes/' . self::$defaults['theme-slug'] );
		$themeDir         = self::translatePath( 'wp-content/themes/' . self::$info['slug'] );

		if ( ! is_dir( $defaultThemeDir ) ) {
			self::writeError( 'Missing theme directory.' );
			return;
		}

		self::writeInfo( 'Changing theme directory name...' );

		// Change the theme directory name.
		if ( ! rename( $defaultThemeDir, $themeDir ) ) {
			self::writeError( 'Failed to rename theme directory.' );
			return;
		}

		self::writeInfo( 'Theme directory name changed.' );

		$files = self::getFilesToChange( $themeDir );

		$search = [
			[
				self::$defaults['function-prefix'],
			],
			[
				'\'' . self::$defaults['text-domain'] . '\'',
				'Text Domain: ' . self::$defaults['text-domain'],
			],
			[
				self::$defaults['project-slug'],
				self::$defaults['host-name'],
				self::$defaults['theme-slug'],
			],
			[
				self::$defaults['project-name'],
				self::$defaults['alt-project-name'],
				self::$defaults['theme-name'],
			],
			[
				self::$defaults['package-name'],
			],
		];

		$replace = [
			self::$info['function'], // Function prefix.
			[
				'\'' . self::$info['text-domain'] . '\'', // Text Domain.
				'Text Domain: ' . self::$info['text-domain'],
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

		self::writeInfo( 'Project files updated!' );
	}

	/**
	 * Require ACF if auth.json file is present.
	 *
	 * @return void
	 */
	public static function maybeRequireACF(): void {
		self::writeInfo( 'Checking for ACF auth.json...' );

		$authPath = self::translatePath( 'wp-content/themes/' . self::$info['slug'] . '/auth.json' );

		if ( ! file_exists( $authPath ) ) {
			self::writeWarning( 'auth.json file not found. Skipping ACF requirement.' );
			return;
		}

		$acfPackage   = 'wpengine/advanced-custom-fields-pro';
		$themePath    = self::translatePath( 'wp-content/themes/' . self::$info['slug'] . '/' );
		$composerData = self::getComposerData( $themePath );

		if ( ! empty( $composerData['require'][ $acfPackage ] ) ) {
			self::writeInfo( 'ACF already required in composer.json.' );
			return;
		}

		$composerData['require'][ $acfPackage ] = "*";

		self::updateComposerData( $composerData, $themePath );

		self::writeInfo( 'ACF Composer dependency updated!' );
	}

	/**
	 * Modify the composer.json project description
	 *
	 * @return void
	 */
	public static function updateComposerDescription(): void {
		self::writeInfo( 'Updating Composer Description...' );

		if ( empty( self::$info['name'] ) ) {
			self::writeError( 'Missing project name.' );
			return;
		}

		$themePath    = self::translatePath( 'wp-content/themes/' . self::$info['slug'] . '/' );
		$composerData = self::getComposerData( $themePath );

		// Update the Description.
		$composerData['description'] = sprintf( 'A custom WordPress Site for %s by Viget.', self::$info['name'] );
		self::updateComposerData( $composerData, $themePath );

		self::writeInfo( 'Composer Description Updated!' );
	}

	/**
	 * Remove the root composer.json file.
	 *
	 * @return void
	 */
	private static function removeRootComposer(): void {
		self::writeInfo( 'Removing root composer.json...' );

		$composerFile = self::translatePath( 'composer.json' );

		if ( ! file_exists( $composerFile ) ) {
			self::writeWarning( 'composer.json file not found. Skipping removal.' );
			return;
		}

		unlink( $composerFile );

		self::writeInfo( 'composer.json file removed!' );
	}

	/**
	 * Remove the packages.json file.
	 *
	 * @return void
	 */
	private static function removePackagesFile(): void {
		self::writeInfo( 'Removing packages.json...' );

		$packagesFile = self::translatePath( 'packages.json' );

		if ( ! file_exists( $packagesFile ) ) {
			self::writeWarning( 'packages.json file not found. Skipping removal.' );
			return;
		}

		unlink( $packagesFile );

		self::writeInfo( 'packages.json file removed!' );
	}

	/**
	 * Get all the files that need to be updated.
	 *
	 * @param string $themeDir
	 *
	 * @return array
	 */
	private static function getFilesToChange( string $themeDir ): array {
		$files = [
			self::translatePath( 'bin/start' ),
			self::translatePath( '.ddev/config.yaml' ),
			self::translatePath( 'composer.json' ),
			$themeDir . '/composer.json',
			$themeDir . '/package.json',
			$themeDir . '/package-lock.json',
			self::translatePath( '.phpcs.xml' ),
			$themeDir . '/.phpcs.xml',
			$themeDir . '/readme.txt',
			self::translatePath( 'README.md' ),
			$themeDir . '/README.md',
			$themeDir . '/style.css',
			$themeDir . '/vite.config.js',
		];

		$themePhpFiles  = glob( $themeDir . '/**/*.php' );
		$themeHtmlFiles = glob( $themeDir . '/**/*.html' );

		return array_merge( $files, $themePhpFiles, $themeHtmlFiles );
	}

	/**
	 * Self Destruct
	 *
	 * @return void
	 */
	private static function destruct(): void {
		self::writeInfo( 'Self-destructing...' );

		// Remove PostCreateProjectScript file
		$createProject = self::translatePath( 'bin/composer-scripts/ProjectEvents/PostCreateProjectScript.php' );
		unlink( $createProject );

		// Remove root composer files
		$composerJson = self::translatePath( 'composer.json' );
		unlink( $composerJson );
		$composerLock = self::translatePath( 'composer.lock' );
		unlink( $composerLock );

		self::writeInfo( 'Self-destruction complete.' );
	}
}
