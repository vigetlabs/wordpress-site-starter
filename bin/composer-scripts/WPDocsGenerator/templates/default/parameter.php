<?php
/**
 * Default Object Template
 *
 * phpcs:disable
 *
 * @var DocItem $object
 * @var int     $offset
 * @var Builder $this
 *
 * @package WPDocsGenerator
 */

use Viget\ComposerScripts\WPDocsGenerator\Builders\Builder;
use Viget\ComposerScripts\WPDocsGenerator\DocItem;

?>
<?php echo $object->getReference(); ?>

<?php if ( count( $object->returnTypes ) > 1 ) : ?>
	types:
		<?php $this->prettyPrint($object->returnTypes); ?>

<?php else : ?>
	type: <?php echo $object->returnTypes[0]; ?>

<?php endif; ?>
	source: <?php echo $object->path . ':' . $object->lineNumber; ?>

	nullable: <?php echo $object->isNullable ? 'true' : 'false'; ?>

	default: <?php echo $object->defaultValue; ?>

