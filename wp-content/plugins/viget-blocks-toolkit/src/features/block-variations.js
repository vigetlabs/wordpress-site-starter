/** global vgtbtVariations */

const unregisterVariations = vgtbtVariations.unregister;

/**
 * WordPress Dependencies
 */
import domReady from '@wordpress/dom-ready';
import '@wordpress/edit-post'; /* dependencies needed to avoid unregisterBlockStyle race condition */
import '@wordpress/edit-site';
import { unregisterBlockVariation } from '@wordpress/blocks';

/**
 * Unregister block variations.
 */
domReady(() => {
	unregisterVariations.forEach((variation) => {
		const [ coreBlock, variationName ] = variation;
		unregisterBlockVariation(
			coreBlock,
			variationName
		);
	});
});
