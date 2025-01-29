<?php
/**
 * Block: Breadcrumbs
 *
 * @global array $block
 *
 * @package WPStarter
 */

$template = $block['template'] ?? []; // phpcs:ignore
?>
<section <?php block_attrs( $block ); ?>>
	<?php wpstarter_breadcrumbs( $template ); ?>
</section>
