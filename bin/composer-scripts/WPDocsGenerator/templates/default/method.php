<?php
/**
 * Class Method Object Template
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
<?php echo $object->getReference(); ?>()
	description: <?php echo $object->description; ?>

	source: <?php echo $object->path . ':' . $object->lineNumber; ?>

	class: <?php echo $object->class; ?>

<?php if ( $object->namespace ) : ?>
	namespace: <?php echo $object->namespace; ?>

<?php endif; ?>
	access: <?php echo $object->access; ?>

	static: <?php echo $object->isStatic ? 'true' : 'false'; ?>

	nullable: <?php echo $object->isNullable ? 'true' : 'false'; ?>

	parameters:<?php
		if( ! $object->parameters ) {
			echo ' none';
		} else {
			echo PHP_EOL . "\t\t";
			echo $this->getBuildObjects( $object->parameters );
		}
		?>

<?php if ( count( $object->returnTypes ) > 1 ) : ?>
	returns:
<?php $this->prettyPrint($object->returnTypes); ?>

<?php else : ?>
	return: <?php echo $object->returnTypes[0]; ?>

<?php endif; ?>

