<?php
/**
 * Render Parts Kit Block.
 *
 * @global string $block_name
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

	<style>
		html {
			height: 100vh;
		}
		body {
			min-height: 100vh;
		}
		.viget-empty-part {
			padding: 1rem;
		}
	</style>
</head>
<body>
	<?php
	if ( $output ) {
		echo $output;
	} else {
		printf(
			'<p class="viget-empty-part"><em>%s</em></p>',
			esc_html__( 'There is nothing to render.', 'viget-parts-kit' )
		);
	}

	wp_footer();
	?>
</body>
</html>
