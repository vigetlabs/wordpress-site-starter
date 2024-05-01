<?php
/**
 * Class Object Template
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
new <?php echo $object->getReference(); ?>()
	description: <?php echo $object->description; ?>

	source: <?php echo $object->path . ':' . $object->lineNumber; ?>

<?php if ( $object->namespace ) : ?>
	namespace: <?php echo $object->namespace; ?>

<?php endif; ?>
	constants:<?php
		if ( ! $object->constants ) :
			echo ' none';
		else :
			echo PHP_EOL . "\t\t";
			echo $this->getBuildObjects( $object->constants );
		endif;
		?>

	properties:<?php
		if ( ! $object->properties ) :
			echo ' none';
		else :
			echo PHP_EOL . "\t\t";
			echo $this->getBuildObjects( $object->properties );
		endif;
		?>

	methods:<?php
		if ( ! $object->methods ) :
			echo ' none';
		else :
			echo PHP_EOL . "\t\t";
			echo $this->getBuildObjects( $object->methods );
		endif;
		?>


