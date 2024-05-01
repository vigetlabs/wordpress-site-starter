<?php
/**
 * Generate Documentation
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts;

use Composer\Script\Event;
use Viget\ComposerScripts\WPDocsGenerator\WPDocsGenerator;

/**
 * WPDocsGeneratorScript
 */
class WPDocsGeneratorScript extends ComposerScript {

	/**
	 * @var ?WPDocsGeneratorScript
	 */
	public static ?WPDocsGeneratorScript $instance = null;

	/**
	 * @var array $config
	 */
	private static array $config = [];

	/**
	 * Generate Documentation
	 *
	 * @param Event $event
	 */
	public static function make( Event $event ): void
	{
		if ( null === self::$instance ) {
			self::$instance = new WPDocsGeneratorScript();
		}

		self::$instance::setEvent( $event );

		if ( self::needsSetup() ) {
			self::setup();
		}

		$defaults = [
			'source'   => null,
			'ignore'   => [ 'vendor', 'node_modules', 'build', 'dist', 'assets', 'acf-json' ],
			'output'   => './docs/',
			'format'   => 'default', // Should be 'markdown' or 'html'. Anything else defaults to flat text.
			'_basedir' => dirname( __FILE__ ) . '/WPDocsGenerator',
		];

		$generator = WPDocsGenerator::getInstance();
		$generator->init( self::$instance, array_merge( $defaults, self::$config ) );
		$generator->generate();
	}

	/**
	 * Check if setup has been run.
	 * @return bool
	 */
	public static function needsSetup(): bool
	{
		return empty( self::$config );
	}

	/**
	 * Run the setup process
	 * @return void
	 */
	public static function setup(): void
	{
		self::$config['source'] = self::getSource();
		self::$config['output'] = self::getOutput();
		self::$config['format'] = self::getFormat();
	}

	/**
	 * Get the source directory
	 * @return string
	 */
	public static function getSource(): string
	{
		$default = ! empty( self::$config['source'] ) ? self::$config['source'] : './';
		$source  = self::ask( 'What is the source directory?', $default );
		$source  = self::translatePath( $source );

		if ( ! is_dir( $source ) ) {
			self::writeError( sprintf( 'The source directory does not exist. (%s)', $source ) );
			return self::getSource();
		}

		if ( ! str_ends_with( $source, '/' ) ) {
			$source .= '/';
		}

		self::writeComment( 'Using Source Directory: ' . $source );

		return $source;
	}

	/**
	 * Get the output directory
	 * @return string
	 */
	public static function getOutput(): string
	{
		$default = ! empty( self::$config['output'] ) ? self::$config['output'] : './docs/';
		$output  = self::ask( 'What is the output directory?', $default );
		$output  = self::translatePath( $output );

		if ( ! is_dir( $output ) ) {
			self::writeWarning( sprintf( 'The source directory does not exist. (%s)', $output ) );

			if ( self::confirm( 'Would you like to create it?' ) ) {
				if ( ! mkdir( $output, 0755, true ) ) { // phpcs:ignore
					self::writeError( 'Failed to create the output directory.' );
					return self::getOutput();
				}

				return $output;
			}

			return self::getOutput();
		}

		if ( ! str_ends_with( $output, '/' ) ) {
			$output .= '/';
		}

		self::writeComment( 'Using Output Directory: ' . $output );
		self::writeWarning( 'Warning: All contents in this directory will be overwritten.' );

		return $output;
	}

	/**
	 * Get the output format
	 * @return string
	 */
	public static function getFormat(): string
	{
		$default = ! empty( self::$config['format'] ) ? self::$config['format'] : 'markdown';
		$format  = self::ask( 'What format should be used?', $default );

		if ( ! in_array( $format, [ 'default', 'markdown', 'html' ] ) ) {
			self::writeWarning( 'Invalid format. Please use "markdown", "html", or "default".' );
			return self::getFormat();
		}

		self::writeComment( 'Using Output Format: ' . $format );

		return $format;
	}
}
