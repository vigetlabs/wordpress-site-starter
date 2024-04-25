<?php
/**
 * Classes Template
 *
 * phpcs:disable
 *
 * @var Builder $this
 *
 * @package WPDocsGenerator
 */

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;

echo 'Classes:' . PHP_EOL . PHP_EOL;

foreach ($this->api->objects as $object ) {
	if ( $object->inApi || 'class' !== $object->node ) {
		continue;
	}

	$referenced = false;

	foreach ( $object->references as $reference ) {
		if ( $reference->assigned ) {
			$referenced = true;
		} elseif ( $reference->returned ) {
			$referenced = true;
		}
	}

	if ( ! $referenced ) {
		continue;
	}

	echo $object->getReference() . PHP_EOL;
}
