<?php
/**
 * ReferenceItem
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

/**
 * ReferenceItem Class
 */
class ReferenceItem {

	/**
	 * @var string
	 */
	public string $reference;

	/**
	 * @var ReferenceUse[]
	 */
	public array $uses = [];

	/**
	 * constructor.
	 *
	 * @param string $reference
	 */
	public function __construct( string $reference ) {
		$this->reference = $reference;
	}

}
