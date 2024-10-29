/** global acfbtStyles */

const unregisterStyles = acfbtStyles.unregister;

/**
 * WordPress Dependencies
 */
import domReady from '@wordpress/dom-ready';
import '@wordpress/edit-post'; /* dependencies needed to avoid unregisterBlockStyle race condition */
import '@wordpress/edit-site';
import { unregisterBlockStyle } from '@wordpress/blocks';

/**
 * Unregister block styles.
 */
domReady(() => {
	unregisterStyles.forEach((style) => {
		const styles = Array.isArray(style[1]) ? style[1] : [style[1]];
		unregisterBlockStyle(
			style[0],
			styles
		);
	});
});
