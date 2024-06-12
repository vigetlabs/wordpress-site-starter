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
		#root {
			padding: 1.5rem 0;
		}

		.viget-empty-part {
			padding: 1rem;
		}

		.viget-source-code {
			background-color: #f0f0f1;
			border-bottom: 1px solid #666;
			border-top: 1px solid #666;
			display: none;
			max-width: 100%;
			padding: 0 2rem;
		}
		.viget-source-code code {
			background-color: transparent;
			font-size: 13px;
			max-width: calc( 100% - 4rem );
			overflow-wrap: break-word;
			overflow: auto;
			text-wrap: wrap;
		}
		.viget-markup {
			margin-top: 2rem;
			position: relative;
		}
		.viget-markup > label {
			border-left: 1px solid transparent;
			border-radius: 0.25rem 0.25rem 0 0;
			border-right: 1px solid transparent;
			border-top: 1px solid transparent;
			cursor: pointer;
			display: block;
			font-size: 11px;
			margin: 0;
			padding: 0.25rem 0.5rem 0.15rem;
			position: absolute;
			right: 1rem;
			text-transform: uppercase;
			top: -1.25rem;
		}
		#viget-toggle-markup {
			height: 1px;
			opacity: 0;
			overflow: hidden;
			position: absolute;
			visibility: hidden;
			width: 1px;
		}
		#viget-toggle-markup:checked + label {
			background-color: #f0f0f1;
			border-color: #666;
			color: #000;
		}
		#viget-toggle-markup:checked + label + .viget-source-code {
			display: block;
		}
	</style>
</head>
<body>
	<main id="root">
		<?php
		if ( $output ) {
			echo $output;
		} else {
			printf(
				'<p class="viget-empty-part"><em>%s</em></p>',
				esc_html__( 'There is nothing to render.', 'viget-parts-kit' )
			);
		}
		?>
	</main>

	<?php wp_footer(); ?>
</body>
</html>
