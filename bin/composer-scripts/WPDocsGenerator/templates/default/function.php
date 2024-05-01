<?php
/**
 * Function Object Template
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

<?php if ( $object->namespace ) : ?>
	namespace: <?php echo $object->namespace; ?>

<?php endif; ?>
	nullable: <?php echo $object->isNullable ? 'true' : 'false'; ?>

	parameters:<?php
		if( ! $object->parameters ) {
			echo ' none';
		} else {
			echo PHP_EOL . "\t\t";
			echo $this->getBuildObjects( $object->parameters);
		}
		?>

	returns:
<?php $this->prettyPrint( $object->returnTypes ); ?>


