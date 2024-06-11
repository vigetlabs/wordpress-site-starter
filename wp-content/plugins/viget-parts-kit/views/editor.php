<?php
/**
 * Render Parts Kit Block Editor.
 *
 * @global string $block_name
 * @global string $output
 *
 * @package VigetPartsKit
 */

$my_cover1 = '<!-- wp:cover /-->';
$my_cover2 = '<!-- wp:cover {"url":{"type":"string"},"useFeaturedImage":{"type":"boolean","default":false},"id":{"type":"number"},"alt":{"type":"string","default":""},"hasParallax":{"type":"boolean","default":false},"isRepeated":{"type":"boolean","default":false},"dimRatio":{"type":"number","default":100},"overlayColor":{"type":"string"},"customOverlayColor":{"type":"string"},"isUserOverlayColor":{"type":"boolean"},"backgroundType":{"type":"string","default":"image"},"focalPoint":{"type":"object"},"minHeight":{"type":"number"},"minHeightUnit":{"type":"string"},"gradient":{"type":"string"},"customGradient":{"type":"string"},"contentPosition":{"type":"string"},"isDark":{"type":"boolean","default":true},"allowedBlocks":{"type":"array"},"templateLock":{"type":["string","boolean"],"enum":["all","insert","contentOnly",false]},"tagName":{"type":"string","default":"div"},"lock":{"type":"object"},"metadata":{"type":"object"},"align":{"type":"string","enum":["left","center","right","wide","full",""]},"style":{"type":"object"},"borderColor":{"type":"string"},"textColor":{"type":"string"},"className":{"type":"string"},"layout":{"type":"object"},"fontSize":{"type":"string"},"fontFamily":{"type":"string"}} /-->';

$cover = '<!-- wp:cover {"layout":{"type":"constrained"}} -->
<div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
<!-- /wp:cover -->';

?>
<!DOCTYPE html>
<html style="margin-top: 0 !important;">
<head>
	<title>Viget Parts Kit - <?php echo esc_html( $block_name ); ?></title>
	<?php wp_head(); ?>

	<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
	<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
	<script src="<?php echo VPK_PLUGIN_URL; ?>build/isolated-block-editor-builtin.js"></script>
	<link rel="stylesheet" href="<?php echo VPK_PLUGIN_URL; ?>build/core.css" />
	<link rel="stylesheet" href="<?php echo VPK_PLUGIN_URL; ?>build/isolated-block-editor.css" />

	<style>
		html {
			height: 100vh;
		}
		body {
			min-height: 100vh;
		}
	</style>
</head>
<body>
	<textarea id="viget-parts-kit-block-editor"><?php echo esc_textarea( $output ); ?></textarea>

	<script>
		wp.attachEditor(
			document.getElementById( 'viget-parts-kit-block-editor' ),
			<?php echo wp_json_encode( $this->gutenberg->get_editor_settings() ) ?>
		);
	</script>

	<?php wp_footer(); ?>
</body>
</html>
