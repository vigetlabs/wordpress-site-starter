<?php
/**
 * Markdown Documentation Builder
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator\Builders;

use Viget\ComposerScripts\WPDocsGenerator\DocItem;

/**
 * MarkdownBuilder Class
 */
class MarkdownBuilder extends Builder
{
	/**
	 * Get the file for the given object.
	 * @param ?DocItem $object
	 * @return string
	 */
	public function getObjectFile( ?DocItem $object = null ): string
	{
		return 'index.md';
	}
}
