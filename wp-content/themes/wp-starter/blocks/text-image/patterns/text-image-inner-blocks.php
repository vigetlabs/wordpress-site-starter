<?php
/**
 * Title: Text & Image (Inner Blocks)
 * Slug: wp-starter/text-image-inner-blocks
 * Categories: text-image
 * Viewport width: 1400
 * Inserter: no
 */

$pattern_dir = dirname( __FILE__ );
?>
<!-- wp:media-text {"imageFill":true,"verticalAlignment":"center"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center is-image-fill"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content"><?php
	require $pattern_dir . '/text-image-media-content.php';
?></div></div>
<!-- /wp:media-text -->
