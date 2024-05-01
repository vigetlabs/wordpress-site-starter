<?php
/**
 * Collects all the code objects for the API
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

/**
 * ApiCollection Class
 */
class ApiCollection extends CodeCollection
{
	/**
	 * @var DocItem[]
	 */
	public array $tree = [];

	/**
	 * @param DocItem $docItem
	 * @return void
	 */
	public function addObject(DocItem $docItem): void
	{
		parent::addObject($docItem);

		$exclude = [
			'property',
			'method',
			'class-constant',
			'parameter'
		];

		if ( ! in_array( $docItem->node, $exclude, true ) ) {
			$this->tree[$docItem->getReference( true )] = $docItem;
		}

	}
}
