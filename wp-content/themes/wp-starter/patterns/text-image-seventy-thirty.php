<?php
/**
 * Title: Text & Image (70/30)
 * Slug: wp-starter/text-image-seventy-thirty
 * Categories: text-image
 * Viewport width: 1400
 */

?>
<!-- wp:acf/text-image {"name":"acf/text-image","data":{},"mode":"preview"} -->
<!-- wp:media-text {"mediaWidth":70} -->
<div class="wp-block-media-text is-stacked-on-mobile" style="grid-template-columns:60% auto"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content"><?php
render_block_core_pattern( [ 'slug' => 'wp-starter/text-image-inner-blocks' ] );
?></div></div>
<!-- /wp:media-text -->
<!-- /wp:acf/text-image -->
