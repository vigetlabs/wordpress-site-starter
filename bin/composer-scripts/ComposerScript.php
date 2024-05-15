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
	 * @param bool   $in_docker
	 *
	 * @return string
	 */
	protected static function translatePath( string $path, bool $in_docker = false ): string
	{
		$base = $in_docker ? '/var/www/html' : getcwd();

		// Support relative paths.
		if ( in_array( $path, [ './', '.' ] ) ) {
			$path = $base;
		} elseif ( str_starts_with( $path, './' ) ) {
			$path = $base . ltrim( $path, '.' );
		} elseif ( ! str_starts_with( $path, '/' ) ) {
			$path = $base . '/' . $path;
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

		$eol = $extraLine ? PHP_EOL : '';

		if ( ! $type ) {
			self::$event->getIO()->write( $content . $eol );
			return;
		}

		self::$event->getIO()->write( sprintf( '<%1$s>%2$s</%1$s>' . $eol, $type, $content ) );
	}

	/**
	 * Output a general message to the terminal window.
	 *
	 * @param string $content
	 * @param bool   $extraLine
	 *
	 * @return void
	 */
	protected static function writeLine( string $content, bool $extraLine = false ): void
	{
		self::writeOutput( $content, '', $extraLine );
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
	 * Get a selection from user input.
	 *
	 * @param string $question
	 * @param array  $options
	 * @param string $default
	 *
	 * @return string
	 */
	protected static function select( string $question, array $options, string $default = '' ): string
	{
		$defaultText = $default ? sprintf( ' <comment>[%s]</comment>', $default ) : '';
		$question    = sprintf( '<question>%s</question>%s ', trim( $question ), $defaultText );

		return self::$event->getIO()->select( $question, $options, $default );
	}

	/**
	 * Run a command in the terminal.
	 *
	 * @param string $cmd
	 *
	 * @return void
	 */
	protected static function runCommand( string $cmd ): void {
		$output = shell_exec( $cmd );

		if ( null !== $output ) {
			self::writeOutput( $output );
		}
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
		$path_options = [
			$file,
			self::getProjectFolder() . '/' . $file,
			self::translatePath( $file ),
		];

		foreach ( $path_options as $path ) {
			if ( ! file_exists( $path ) ) {
				continue;
			}
			break;
		}

		if ( ! file_exists( $path ) ) {
			self::writeWarning( sprintf( 'File does not exist: %s', $file ) );
			return;
		}

		$contents = file_get_contents( $path );

		if ( is_string( $search ) && ! str_contains( $contents, $search ) ) {
			return;
		}

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

	/**
	 * Get the composer data.
	 *
	 * @param string $themePath
	 *
	 * @return array
	 */
	public static function getComposerData( string $themePath ): array {
		$path = self::translatePath( $themePath . 'composer.json' );
		$json = file_get_contents( $path );
		$data = json_decode( $json, true );

		if ( ! $data ) {
			return [];
		}

		return $data;
	}

	/**
	 * Update the composer data.
	 *
	 * @param array $data
	 * @param string $themePath
	 *
	 * @return void
	 */
	public static function updateComposerData( array $data, string $themePath ): void {
		$path = self::translatePath( $themePath . 'composer.json' );

		file_put_contents(
			$path,
			json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
		);
	}

	/**
	 * Wait for a specified number of seconds.
	 *
	 * @param int $seconds
	 *
	 * @return void
	 */
	protected static function wait( int $seconds = 1 ): void {
		sleep( $seconds );
	}

	/**
	 * Escape quotes in a string.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected static function escapeQuotes( string $string ): string {
		return str_replace( '"', '\"', $string );
	}

	/**
	 * Render the Viget logo.
	 *
	 * @return void
	 */
	protected static function renderVigetLogo(): void {
		$logo = <<<VIGET
                          <fg=#F26D20>.::::.</>
                        <fg=#F26D20>-========-</>
                       <fg=#F26D20>-===========</>
       <fg=#1296BB>..:::::::..</>     <fg=#F26D20>:==========-</>
    <fg=#1296BB>.:-===========--.</>   <fg=#F26D20>.-======-:</>
  <fg=#1296BB>.-=================-.</>     <fg=#F26D20>..</>
 <fg=#1296BB>.=====================.</>
 <fg=#1296BB>:=====================-</>
 <fg=#1296BB>:=-===================-</>
  <fg=#1296BB>-===================-</>
   <fg=#1296BB>:-================:</>
     <fg=#1296BB>.:-=========--:</>
         <fg=#1296BB>.......</>
VIGET;

		self::writeOutput( $logo . PHP_EOL );
	}

	/**
	 * Render a centered message.
	 *
	 * @param array $lines
	 * @param int $padding
	 * @param ?string $border
	 * @param ?string $borderColor
	 *
	 * @return string
	 */
	protected static function centeredText( array $lines, int $padding = 2, ?string $border = null, ?string $borderColor = null ): string {
		$maxLength = 0;

		foreach ( $lines as $line ) {
			$plain = strip_tags( $line );
			if ( strlen( $plain ) > $maxLength ) {
				$maxLength = strlen( $plain );
			}
		}

		// Calculate the total length of each line with padding and border
		$borderLength = $border ? strlen( $border ) * 2 : 0;
		$totalLength = $maxLength + ( $padding * 2 ) + $borderLength;
		$borderStartTag = $borderColor ? "<fg=$borderColor>" : '';
		$borderEndTag = $borderColor ? '</>' : '';
		$borderText = $border ? $borderStartTag . $border . $borderEndTag : '';
		$borderLine = $borderStartTag . str_repeat( $border, $totalLength ) . $borderEndTag . PHP_EOL;

		$output = '';

		// Add the top border.
		if ( $border ) {
			$output .= $borderLine;
		}

		// Add each line with padding and centering
		foreach ( $lines as $line ) {
			if ( $border ) {
				$output .= $borderText;
			}

			$output .= self::getPaddedLine( $line, $maxLength, $padding );

			if ( $border ) {
				$output .= $borderText;
			}

			$output .= PHP_EOL;
		}

		// Add the bottom border
		if ( $border ) {
			$output .= $borderLine;
		}

		return $output;
	}

	/**
	 * Get a line with padding and centering.
	 *
	 * @param string $line
	 * @param int $maxLength
	 * @param int $padding
	 *
	 * @return string
	 */
	private static function getPaddedLine( string $line, int $maxLength, int $padding ): string {
		$plainText = strip_tags($line);
		$plainLength = strlen($plainText);

		$totalPadding = $maxLength - $plainLength;
		$leftPadding = str_repeat(' ', floor($totalPadding / 2) + $padding);
		$rightPadding = str_repeat(' ', ceil($totalPadding / 2) + $padding);

		$result = $leftPadding;
		$htmlTag = false;

		for ($i = 0; $i < strlen($line); $i++) {
			if ($line[$i] === '<') {
				$htmlTag = true;
				$result .= '<';
			} elseif ($line[$i] === '>') {
				$htmlTag = false;
				$result .= '>';
			} elseif ($htmlTag) {
				$result .= $line[$i];
			} else {
				$result .= $line[$i];
			}
		}

		$result .= $rightPadding;

		return $result;
	}

	/**
	 * Generate a random password.
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	protected static function generatePassword( int $length = 16 ): string {
		$characters = 'abcdefghijkmnpqrstuvwxyzCDEFGHJKLMNPQRTUVWXY3679!@#%^&*?,.()[]{}';

		$pass = '';
		$max = strlen( $characters ) - 1;

		for ( $i = 0; $i < $length; ++$i ) {
			$pass .= $characters[ mt_rand( 0, $max ) ];
		}

		return $pass;
	}
}
