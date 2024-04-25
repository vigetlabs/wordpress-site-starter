<?php
/**
 * Base class when interfacing with Composer.
 *
 * The following tags can be used to format output:
 * <comment>: Used for comments or less important information.
 * <question>: Used for questions or prompts to the user.
 * <error>: Used for displaying error messages or important warnings.
 * <warning>: Similar to <error>, used for displaying warnings.
 * <fg=color>: Used to set the foreground color. Replace 'color' with the desired color, such as red, green, yellow, etc.
 * <bg=color>: Used to set the background color. Replace 'color' with the desired color.
 *
 * @package ComposerScripts
 */

namespace Viget\ComposerScripts;

use Composer\Script\Event;
use Composer\Composer;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * ComposerScript
 */
class ComposerScript {

	/**
	 * The Event object
	 * @var ?Event
	 */
	public static ?Event $event = null;

	/**
	 * Composer Instance
	 * @var ?Composer
	 */
	public static ?Composer $composer = null;

	/**
	 * Console Output
	 * @var ?ConsoleOutput
	 */
	public static ?ConsoleOutput $output = null;

	/**
	 * Set the event object
	 *
	 * @param Event $event
	 * @return void
	 */
	protected static function setEvent( Event $event ): void
	{
		self::$event    = $event;
		self::$composer = self::$event->getComposer();
		self::$output   = new ConsoleOutput();
	}

	/**
	 * Translate a relative path to an absolute path.
	 *
	 * @param string $path
	 * @return string
	 */
	protected static function translatePath( string $path ): string
	{
		// Support relative paths.
		if ( in_array( $path, [ './', '.' ] ) ) {
			$path = getcwd();
		} elseif ( str_starts_with( $path, './' ) ) {
			$path = getcwd() . ltrim( $path, '.' );
		} elseif ( ! str_starts_with( $path, '/' ) ) {
			$path = getcwd() . '/' . $path;
		}

		return $path;
	}

	/**
	 * Create a custom output style.
	 *
	 * @param string $tag
	 * @param string $color
	 * @param string $background
	 * @param array  $options
	 * @return void
	 */
	protected static function customStyle( string $tag, string $color, string $background = '', array $options = [] ): void
	{
		$style = new OutputFormatterStyle( $color, $background, $options );
		self::$output->getFormatter()->setStyle( $tag, $style );
	}

	/**
	 * Output content to the terminal window.
	 *
	 * @param string $content
	 * @param string $type
	 * @param bool   $extraLine
	 *
	 * @return void
	 */
	protected static function writeOutput( string $content, string $type = '', bool $extraLine = false ): void
	{
		if ( ! self::$event ) {
			echo 'Missing event object.' . PHP_EOL;
			return;
		}

		if ( ! $type ) {
			self::$event->getIO()->write( $content );
			return;
		}

		$eol = $extraLine ? PHP_EOL : '';

		self::$event->getIO()->write( sprintf( '<%1$s>%2$s</%1$s>' . $eol, $type, $content ) );
	}

	/**
	 * Output an info message to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeInfo( string $content ): void
	{
		self::writeOutput( $content, 'info' );
	}

	/**
	 * Output a comment to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeComment( string $content ): void
	{
		self::writeOutput( $content, 'comment' );
	}

	/**
	 * Output an error to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeError( string $content ): void
	{
		self::writeOutput( $content, 'error' );
	}

	/**
	 * Output a warning to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeWarning( string $content ): void
	{
		self::writeOutput( $content, 'warning' );
	}

	/**
	 * Get a response from user input.
	 *
	 * @param string $question
	 * @param string $default
	 *
	 * @return string
	 */
	protected static function ask( string $question, string $default = '' ): string
	{
		$defaultText = $default ? sprintf( ' <comment>[%s]</comment>', $default ) : '';
		$ask          = sprintf( '<question>%s</question>%s ', trim( $question ), $defaultText );

		return self::$event->getIO()->ask( $ask, $default );
	}

	/**
	 * Get confirmation from user input.
	 *
	 * @param string $question
	 * @param bool $default
	 *
	 * @return string
	 */
	protected static function confirm( string $question, bool $default = true ): string
	{
		$options      = $default ? 'Y/n' : 'y/N';
		$confirmation = sprintf( '<info>%s</info> <comment>[%s]</comment> ', trim( $question ), $options );

		return self::$event->getIO()->askConfirmation( $confirmation, $default );
	}

	/**
	 * Get the project folder.
	 *
	 * @return ?string
	 */
	protected static function getProjectFolder(): ?string {
		if ( ! self::$event ) {
			echo 'Missing event object.' . PHP_EOL;
			return null;
		}

		$vendor_dir = self::$event->getComposer()->getConfig()->get( 'vendor-dir' );

		return realpath( $vendor_dir . '/../' );
	}

	/**
	 * Replace a string in a file.
	 *
	 * @param string|array $search
	 * @param string|array $replace
	 * @param string $file
	 *
	 * @return void
	 */
	protected static function searchReplaceFile( string|array $search, string|array $replace, string $file ): void {
		if ( file_exists( $file ) ) {
			$path = $file;
		} else {
			if ( ! file_exists( self::getProjectFolder() . '/' . $file ) ) {
				return;
			}

			$path = self::getProjectFolder() . '/' . $file;
		}

		$contents = file_get_contents( $path );
		$contents = str_replace( $search, $replace, $contents );
		file_put_contents( $path, $contents );
	}

	/**
	 * Delete a directory and all of its contents.
	 *
	 * @param string $path
	 *
	 * @return void
	 */
	protected static function deleteDirectory( string $path ): void {
		$files = array_diff( scandir( $path ), [ '.', '..' ] );

		foreach ( $files as $file ) {
			$item = $path . '/' . $file;

			if ( is_dir( $item ) ) {
				self::deleteDirectory( $item );
			} else {
				unlink( $item );
			}
		}

		rmdir( $path );
	}
}
