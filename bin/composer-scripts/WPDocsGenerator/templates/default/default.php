<?php
/**
 * Default Object Template
 *
 * phpcs:disable
 *
 * @var DocItem $object
 * @var Builder $this
 *
 * @package WPDocsGenerator
 */

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;
use Viget\ComposerScripts\WPDocsGenerator\DocItem;

?>
<?php echo $object->getReference(); ?>

	node: <?php echo $object->node; ?>

	description: <?php echo $object->description; ?>

	source: <?php echo $object->path . ':' . $object->lineNumber; ?>

	namespace: <?php echo $object->namespace; ?>

	object:
		<?php $this->prettyPrint( $object, 2 ); ?>

