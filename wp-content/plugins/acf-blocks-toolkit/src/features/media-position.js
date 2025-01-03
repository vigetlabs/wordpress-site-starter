/**
 * WordPress Dependencies.
 */
import { addFilter } from '@wordpress/hooks';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { getBlockSupport } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';

const MediaPosition = BlockEdit => {
	return props => {
		// Early return if block doesn't support attributes
		if (!props.attributes || typeof props.attributes !== 'object') {
			return <BlockEdit {...props} />;
		}

		const mediaPositionSupport = getBlockSupport( props.name, 'mediaPosition', false );

		if ( ! mediaPositionSupport?.transformations ) {
			return <BlockEdit {...props} />;
		}

		const { attributes, setAttributes, clientId } = props;
		const className = attributes.className || '';
		const classes = className.split( ' ' );
		const currentPosition = attributes.mediaPosition || 'left';

		const { replaceInnerBlocks } = useDispatch( 'core/block-editor' );
		const { getBlocks } = useSelect( select => ({
			getBlocks: select( 'core/block-editor' ).getBlocks,
		}));

		const findTransformationRule = (blockName, transformations) => {
			return transformations.find(t => Object.keys(t)[0] === blockName)?.[blockName];
		};

		const transformBlock = (block, transformations, parentInnerBlocks = null, newPosition) => {
			const positionToUse = newPosition || currentPosition;

			let newBlock = { ...block };
			let newInnerBlocks = [...block.innerBlocks];

			// Helper function to apply attribute transformations
			const applyAttributes = (currentAttrs, transformRules) => {

				// Create a new attributes object that includes all current attributes
				let newAttrs = { ...currentAttrs };

				// Process each transformation rule
				Object.entries(transformRules).forEach(([attr, values]) => {
					// If the value is an object but doesn't have position keys, it's a nested attribute
					if (typeof values === 'object' && !values[positionToUse]) {
						newAttrs[attr] = applyAttributes(
							newAttrs[attr] || {},
							values
						);
					} else {
						// Get the new value for the current position
						const newValue = values[positionToUse];
						if (newValue !== undefined) {
							newAttrs[attr] = newValue;
						}
					}
				});

				return newAttrs;
			};

			// First check for root level transformations
			const rootRule = findTransformationRule(block.name, transformations);

			if (rootRule) {
				if (rootRule.attributes) {
					newBlock.attributes = applyAttributes(newBlock.attributes, rootRule.attributes);
				}

				if (rootRule.reverse) {
					newInnerBlocks = newInnerBlocks.reverse();
				}

				if (rootRule.innerBlocks) {
					newInnerBlocks = newInnerBlocks.map(innerBlock =>
						transformBlock(innerBlock, transformations, rootRule.innerBlocks, positionToUse)
					);
					return {
						...newBlock,
						innerBlocks: newInnerBlocks
					};
				}
			}

			// Check for parent-specific transformations
			if (parentInnerBlocks) {
				const innerBlockRule = parentInnerBlocks.find(t => Object.keys(t)[0] === block.name)?.[block.name];

				if (innerBlockRule) {
					if (innerBlockRule.attributes) {
						// Apply the transformations and ensure we're setting the new attributes
						const transformedAttributes = applyAttributes(newBlock.attributes, innerBlockRule.attributes);
						newBlock = {
							...newBlock,
							attributes: transformedAttributes
						};
					}

					if (innerBlockRule.innerBlocks) {
						newInnerBlocks = newInnerBlocks.map(innerBlock =>
							transformBlock(innerBlock, transformations, innerBlockRule.innerBlocks, positionToUse)
						);
						return {
							...newBlock,
							innerBlocks: newInnerBlocks
						};
					}
				}
			}

			// Continue traversing children with root transformations
			newInnerBlocks = newInnerBlocks.map(innerBlock =>
				transformBlock(innerBlock, transformations, null, positionToUse)
			);

			return {
				...newBlock,
				innerBlocks: newInnerBlocks
			};
		};

		const setMediaPosition = (position) => {
			const newClasses = classes.filter(c =>
				!['has-media-on-the-left', 'has-media-on-the-right'].includes(c)
			);
			newClasses.push(`has-media-on-the-${position}`);

			// Get the blocks BEFORE updating attributes
			const innerBlocks = getBlocks(clientId);
			if (!innerBlocks?.length) return;

			// Transform blocks using the new position value
			const transformedBlocks = innerBlocks.map(block =>
				transformBlock(block, mediaPositionSupport.transformations, null, position)
			);

			// Update attributes and blocks together
			setAttributes({
				className: newClasses.join(' ').trim(),
				mediaPosition: position,
			});
			replaceInnerBlocks(clientId, transformedBlocks, false);
		};

		return (
			<>
				<BlockControls group="block">
					<ToolbarGroup>
						<ToolbarButton
							icon="align-pull-left"
							title={ __( 'Show media on left' ) }
							onClick={ () => setMediaPosition( 'left' ) }
							isActive={ currentPosition === 'left' }
						/>
						<ToolbarButton
							icon="align-pull-right"
							title={ __( 'Show media on right' ) }
							onClick={ () => setMediaPosition( 'right' ) }
							isActive={ currentPosition === 'right' }
						/>
					</ToolbarGroup>
				</BlockControls>
				<BlockEdit {...props} />
			</>
		);
	};
};

addFilter( 'editor.BlockEdit', 'acf-bt/media-position', MediaPosition );
