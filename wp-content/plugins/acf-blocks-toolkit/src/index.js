/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	PanelRow,
	ToggleControl,
	__experimentalGrid as Grid, // eslint-disable-line
} from '@wordpress/components';

import icons from '../build/icons.json';

/**
 * All available icons.
 * (Order determines presentation order)
 */
export const ICONS = icons;

/**
 * Add the attributes needed for button icons.
 *
 * @since 0.1.0
 * @param {Object} settings
 */
function addAttributes(settings) {
	if ('core/button' !== settings.name) {
		return settings;
	}

	// Add the block visibility attributes.
	const iconAttributes = {
		icon: {
			type: 'string',
		},
		iconPositionLeft: {
			type: 'boolean',
			default: false,
		},
	};

	return {
		...settings,
		attributes: {
			...settings.attributes,
			...iconAttributes,
		},
	};
}

addFilter(
	'blocks.registerBlockType',
	'acf-blocks-toolkit/add-attributes',
	addAttributes,
);

/**
 * Filter the BlockEdit object and add icon inspector controls to button blocks.
 *
 * @since 0.1.0
 * @param {Object} BlockEdit
 */
function addInspectorControls(BlockEdit) {
	return (props) => {
		if (props.name !== 'core/button') {
			return <BlockEdit {...props} />;
		}

		const { attributes, setAttributes } = props;
		const { icon: currentIcon, iconPositionLeft } = attributes;

		return (
			<>
				<BlockEdit {...props} />
				<InspectorControls>
					<PanelBody
						title={__('Icon', 'acf-blocks-toolkit')}
						className="button-icon-picker"
						initialOpen={true}
					>
						<PanelRow>
							<Grid className="button-icon-picker__grid" columns="5" gap="0">
								{ICONS.map((icon, index) => (
									<Button
										key={index}
										label={icon?.label}
										isPressed={currentIcon === icon.value}
										className="button-icon-picker__button"
										onClick={() =>
											setAttributes({
												// Allow user to disable icons.
												icon: currentIcon === icon.value ? null : icon.value,
											})
										}
									>
										<span
											dangerouslySetInnerHTML={{
												__html: icon.icon ?? icon.value,
											}}
										/>
									</Button>
								))}
							</Grid>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__('Show icon on left', 'acf-blocks-toolkit')}
								checked={iconPositionLeft}
								onChange={() => {
									setAttributes({
										iconPositionLeft: !iconPositionLeft,
									});
								}}
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}

addFilter(
	'editor.BlockEdit',
	'acf-blocks-toolkit/add-inspector-controls',
	addInspectorControls,
);

/**
 * Add icon and position classes in the Editor.
 *
 * @since 0.1.0
 * @param {Object} BlockListBlock
 */
function addClasses(BlockListBlock) {
	return (props) => {
		const { name, attributes } = props;

		if ('core/button' !== name || !attributes?.icon) {
			return <BlockListBlock {...props} />;
		}

		const classes = classnames(props?.className, {
			[`has-icon__${attributes?.icon}`]: attributes?.icon,
			'has-icon-position__left': attributes?.iconPositionLeft,
		});

		return <BlockListBlock {...props} className={classes} />;
	};
}

addFilter(
	'editor.BlockListBlock',
	'acf-blocks-toolkit/add-classes',
	addClasses,
);
