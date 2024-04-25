<?php
/**
 * ReferenceUse
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

/**
 * DocItem Class
 */
class ReferenceUse {

	/**
	 * @var string
	 */
	public string $path;

	/**
	 * @var int
	 */
	public int $line;

	/**
	 * @var bool
	 */
	public bool $assigned = false;

	/**
	 * @var bool
	 */
	public bool $returned = false;

	/**
	 * constructor.
	 *
	 * @param string $path
	 * @param int $line
	 */
	public function __construct( string $path, int $line ) {
		$this->path = $path;
		$this->line = $line;
	}
}
