<?php
/**
 * Title: Text & Image (60/40)
 * Slug: wp-starter/text-image-sixty-forty
 * Categories: text-image
 * Viewport width: 1400
 */

$pattern_dir = dirname( __FILE__ );
?>
<!-- wp:acf/text-image {"name":"acf/text-image","data":{},"mode":"preview"} -->
<!-- wp:media-text {"imageFill":true,"mediaWidth":60,"verticalAlignment":"center"} -->
<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center is-image-fill" style="grid-template-columns:60% auto"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content"><?php
	require $pattern_dir . '/text-image-media-content.php';
?></div></div>
<!-- /wp:media-text -->
<!-- /wp:acf/text-image -->
