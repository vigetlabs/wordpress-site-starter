<?php
/**
 * Hooks Template
 *
 * phpcs:disable
 *
 * @var Builder $this
 *
 * @package WPDocsGenerator
 */

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;

echo 'Hooks:' . PHP_EOL . PHP_EOL;

foreach ($this->hooks->objects as $object ) {
	var_dump( $object );
}
