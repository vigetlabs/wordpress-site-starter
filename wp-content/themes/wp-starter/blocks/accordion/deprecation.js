/**
 * Accordion Block Deprecation Migration
 *
 * Migrates acf/accordion blocks to core/accordion blocks.
 *
 * @package WPStarter
 */

(function () {
	if (
		typeof wp === 'undefined' ||
		!wp.blocks ||
		!wp.hooks ||
		!wp.blocks.getBlockType
	) {
		return;
	}

	/**
	 * Migration function to convert acf/accordion to core/accordion.
	 *
	 * @param {Object} attributes The block attributes.
	 * @param {Array}  innerBlocks The inner blocks.
	 * @return {Array} New attributes and inner blocks.
	 */
	function migrateToCore(attributes, innerBlocks) {
		const newInnerBlocks = [];

		innerBlocks.forEach(function (innerBlock) {
			if (innerBlock.name === 'core/details') {
				const detailsAttrs = innerBlock.attributes || {};
				const blockInnerBlocks = innerBlock.innerBlocks || [];

				// Extract summary text from attributes.summary.
				// The core/details block stores the summary text in the summary attribute.
				// It may be a RichText object, so we need to extract the text properly.
				let summaryText = '';
				let contentBlocks = [];

				if (detailsAttrs.summary) {
					const summaryValue = detailsAttrs.summary;

					// Handle RichText object (has originalHTML property).
					if (
						typeof summaryValue === 'object' &&
						summaryValue !== null &&
						summaryValue.originalHTML !== undefined
					) {
						// Extract text from RichText originalHTML.
						const div = document.createElement('div');
						div.innerHTML = summaryValue.originalHTML;
						summaryText = div.textContent || div.innerText || '';
					} else if (typeof summaryValue === 'string') {
						// It's already a string.
						summaryText = summaryValue;
					} else {
						// Try to convert to string as fallback.
						summaryText = String(summaryValue);
					}
				}

				// The content is in innerBlocks.
				contentBlocks = blockInnerBlocks;

				// Build accordion-item structure.
				const accordionItemInner = [];

				// Add accordion-heading if we have summary text.
				// The core/accordion-heading block uses a 'title' attribute, not inner blocks.
				if (summaryText) {
					const headingBlock = wp.blocks.createBlock('core/accordion-heading', {
						title: summaryText,
					});
					accordionItemInner.push(headingBlock);
				}

				// Add accordion-panel with content blocks.
				const panelBlock = wp.blocks.createBlock(
					'core/accordion-panel',
					{},
					contentBlocks
				);
				accordionItemInner.push(panelBlock);

				// Create accordion-item block.
				const accordionItem = wp.blocks.createBlock(
					'core/accordion-item',
					{
						open: detailsAttrs.open || false,
					},
					accordionItemInner
				);
				newInnerBlocks.push(accordionItem);
			} else {
				// Keep other blocks as-is.
				newInnerBlocks.push(innerBlock);
			}
		});

		// Return new attributes (empty for core/accordion) and converted inner blocks.
		return [{}, newInnerBlocks];
	}

	/**
	 * Check if block is eligible for migration.
	 *
	 * @param {Object} attributes The block attributes.
	 * @param {Array}  innerBlocks The inner blocks.
	 * @return {boolean} Whether the block is eligible for migration.
	 */
	function isEligibleForMigration(attributes, innerBlocks) {
		return innerBlocks && innerBlocks.length > 0;
	}

	// Make functions available globally for PHP reference.
	window.acfAccordionMigrateToCore = migrateToCore;
	window.acfAccordionIsEligibleForMigration = isEligibleForMigration;

	/**
	 * Transform function to convert acf/accordion to core/accordion.
	 * This is used for block transforms.
	 *
	 * @param {Object} attributes The block attributes.
	 * @param {Array}  innerBlocks The inner blocks.
	 * @return {Object} Transformed block data.
	 */
	function transformToCore(attributes, innerBlocks) {
		const [, newInnerBlocks] = migrateToCore(attributes, innerBlocks);
		return {
			attributes: {
				className: 'acf-block-accordion acf-block-accordion-migrated',
			},
			innerBlocks: newInnerBlocks,
		};
	}

	// Register deprecation via WordPress hooks.
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'acf/accordion-deprecation',
		function (settings, name) {
			if (name !== 'acf/accordion') {
				return settings;
			}

			// Hide block from inserter by making it unavailable for insertion.
			settings.supports = settings.supports || {};
			settings.supports.inserter = false;

			// Add deprecation if not already set.
			if (!settings.deprecated || settings.deprecated.length === 0) {
				settings.deprecated = [
					{
						attributes: {},
						supports: settings.supports || {},
						migrate: function (attrs, innerBlocks) {
							// Migrate and also change block type to core/accordion.
							const [, newInnerBlocks] = migrateToCore(attrs, innerBlocks);
							// Return the migrated data with CSS classes.
							// Note: The block type change is handled separately via block replacement.
							return [
								{
									className: 'acf-block-accordion acf-block-accordion-migrated',
								},
								newInnerBlocks,
							];
						},
						isEligible: isEligibleForMigration,
					},
				];
			}

			// Add transform to convert to core/accordion.
			if (!settings.transforms) {
				settings.transforms = {};
			}
			if (!settings.transforms.to) {
				settings.transforms.to = [];
			}

			// Check if transform already exists.
			const hasTransform = settings.transforms.to.some(function (transform) {
				return (
					transform.type === 'block' &&
					transform.blocks &&
					transform.blocks.includes('core/accordion')
				);
			});

			if (!hasTransform) {
				settings.transforms.to.push({
					type: 'block',
					blocks: ['core/accordion'],
					transform: transformToCore,
					priority: 20,
				});
			}

			return settings;
		}
	);

	// Automatically convert acf/accordion blocks to core/accordion when editor loads.
	// This uses the editor's data store to replace blocks after they're loaded.
	if (typeof wp !== 'undefined' && wp.data && wp.data.subscribe) {
		let hasConverted = false;

		wp.data.subscribe(function () {
			// Only run once per editor load.
			if (hasConverted) {
				return;
			}

			// Wait a bit for blocks to be fully loaded.
			setTimeout(function () {
				if (
					!wp.data.select ||
					!wp.data.dispatch ||
					hasConverted
				) {
					return;
				}

				const select = wp.data.select('core/block-editor') || wp.data.select('core/editor');
				const dispatch = wp.data.dispatch('core/block-editor') || wp.data.dispatch('core/editor');

				if (!select || !dispatch || !select.getBlocks) {
					return;
				}

				// Find all acf/accordion blocks.
				function findBlocks(blocks, replacements) {
					blocks.forEach(function (block) {
						if (block.name === 'acf/accordion') {
							const [, newInnerBlocks] = migrateToCore(
								block.attributes || {},
								block.innerBlocks || []
							);

							// Create core/accordion block with migration CSS classes.
							const migratedBlock = wp.blocks.createBlock(
								'core/accordion',
								{
									className: 'acf-block-accordion acf-block-accordion-migrated',
								},
								newInnerBlocks
							);

							replacements.push({
								clientId: block.clientId,
								block: migratedBlock,
							});
						}

						if (block.innerBlocks && block.innerBlocks.length > 0) {
							findBlocks(block.innerBlocks, replacements);
						}
					});
				}

				const allBlocks = select.getBlocks();
				const replacements = [];
				findBlocks(allBlocks, replacements);

				// Replace all acf/accordion blocks with core/accordion.
				if (replacements.length > 0 && dispatch.replaceBlocks) {
					replacements.forEach(function (replacement) {
						dispatch.replaceBlocks(replacement.clientId, replacement.block);
					});
					hasConverted = true;
				}
			}, 500);
		});
	}
})();

