<?php
/**
 * Disable the InnerBlocks wrapper for the Navigation Container block.
 *
 * @package WPStarter
 */

add_filter(
	'acf/blocks/wrap_frontend_innerblocks',
	function ( $wrap, $name ) {
		if ( $name !== 'acf/navigation-container' ) {
			return $wrap;
		}
		return false;
	},
	10,
	2
);
