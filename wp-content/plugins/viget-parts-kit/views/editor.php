<?php
/**
 * Render Parts Kit Block Editor.
 *
 * @global string $block_name
 *
 * @global string $output
 *
 * @package VigetPartsKit
 */

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
		body/*,
		#viget-parts-kit-block-editor,
		body > .editor,
		body > .editor > .block-editor,
		body > .editor > .block-editor > div,
		body > .editor > .block-editor > div > .edit-post-layout*/ {
			min-height: 100vh;
		}

		/*
		.interface-navigable-region > *:not(:empty),
		.edit-post-visual-editor,
		.is-root-container {
			height: 100%;
		}*/
	</style>
</head>
<body>
	<textarea id="viget-parts-kit-block-editor"><?php echo esc_textarea( $output ); ?></textarea>

	<script>
		wp.attachEditor( document.getElementById( 'viget-parts-kit-block-editor' ) );
	</script>

	<?php wp_footer(); ?>
</body>
</html>
