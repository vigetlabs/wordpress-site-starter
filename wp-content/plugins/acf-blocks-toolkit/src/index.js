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
import {
	arrowRight,
	arrowLeft,
	chevronLeft,
	chevronLeftSmall,
	chevronRight,
	chevronRightSmall,
	cloud,
	cloudUpload,
	commentAuthorAvatar,
	download,
	external,
	help,
	info,
	lockOutline,
	login,
	next,
	previous,
	shuffle,
	wordpress,
} from '@wordpress/icons';

/**
 * All available icons.
 * (Order determines presentation order)
 */
export const ICONS = [
	{
		label: __( 'Chevron Right', 'acf-blocks-toolkit' ),
		value: 'chevron-right',
		icon: chevronRight,
	},
	{
		label: __( 'Chevron Left', 'acf-blocks-toolkit' ),
		value: 'chevron-left',
		icon: chevronLeft,
	},
	{
		label: __( 'Chevron Right (Small)', 'acf-blocks-toolkit' ),
		value: 'chevron-right-small',
		icon: chevronRightSmall,
	},
	{
		label: __( 'Chevron Left (Small)', 'acf-blocks-toolkit' ),
		value: 'chevron-left-small',
		icon: chevronLeftSmall,
	},
	{
		label: __( 'Shuffle', 'acf-blocks-toolkit' ),
		value: 'shuffle',
		icon: shuffle,
	},
	{
		label: __( 'Arrow Right', 'acf-blocks-toolkit' ),
		value: 'arrow-right',
		icon: arrowRight,
	},
	{
		label: __( 'Arrow Left', 'acf-blocks-toolkit' ),
		value: 'arrow-left',
		icon: arrowLeft,
	},
	{
		label: __( 'Next', 'acf-blocks-toolkit' ),
		value: 'next',
		icon: next,
	},
	{
		label: __( 'Previous', 'acf-blocks-toolkit' ),
		value: 'previous',
		icon: previous,
	},
	{
		label: __( 'Download', 'acf-blocks-toolkit' ),
		value: 'download',
		icon: download,
	},
	{
		label: __( 'External Arrow', 'acf-blocks-toolkit' ),
		value: 'external-arrow',
		icon: (
			<svg
				width="24"
				height="24"
				viewBox="0 0 24 24"
				xmlns="http://www.w3.org/2000/svg"
			>
				<polygon points="18 6 8.15240328 6 8.15240328 8.1101993 14.3985932 8.1101993 6 16.5087925 7.4912075 18 15.8898007 9.6014068 15.8898007 15.8475967 18 15.8475967"></polygon>
			</svg>
		),
	},
	{
		label: __( 'External', 'acf-blocks-toolkit' ),
		value: 'external',
		icon: external,
	},
	{
		label: __( 'Login', 'acf-blocks-toolkit' ),
		value: 'login',
		icon: login,
	},
	{
		label: __( 'Lock', 'acf-blocks-toolkit' ),
		value: 'lock-outline',
		icon: lockOutline,
	},
	{
		label: __( 'Avatar', 'acf-blocks-toolkit' ),
		value: 'comment-author-avatar',
		icon: commentAuthorAvatar,
	},
	{
		label: __( 'Cloud', 'acf-blocks-toolkit' ),
		value: 'cloud',
		icon: cloud,
	},
	{
		label: __( 'Cloud Upload', 'acf-blocks-toolkit' ),
		value: 'cloud-upload',
		icon: cloudUpload,
	},
	{
		label: __( 'Help', 'acf-blocks-toolkit' ),
		value: 'help',
		icon: help,
	},
	{
		label: __( 'Info', 'acf-blocks-toolkit' ),
		value: 'info',
		icon: info,
	},
	{
		label: __( 'WordPress', 'acf-blocks-toolkit' ),
		value: 'wordpress',
		icon: wordpress,
	},
];

/**
 * Add the attributes needed for button icons.
 *
 * @since 0.1.0
 * @param {Object} settings
 */
function addAttributes( settings ) {
	if ( 'core/button' !== settings.name ) {
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
	addAttributes
);

/**
 * Filter the BlockEdit object and add icon inspector controls to button blocks.
 *
 * @since 0.1.0
 * @param {Object} BlockEdit
 */
function addInspectorControls( BlockEdit ) {
	return ( props ) => {
		if ( props.name !== 'core/button' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const { icon: currentIcon, iconPositionLeft } = attributes;

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Icon', 'acf-blocks-toolkit' ) }
						className="button-icon-picker"
						initialOpen={ true }
					>
						<PanelRow>
							<Grid
								className="button-icon-picker__grid"
								columns="5"
								gap="0"
							>
								{ ICONS.map( ( icon, index ) => (
									<Button
										key={ index }
										label={ icon?.label }
										isPressed={ currentIcon === icon.value }
										className="button-icon-picker__button"
										onClick={ () =>
											setAttributes( {
												// Allow user to disable icons.
												icon:
													currentIcon === icon.value
														? null
														: icon.value,
											} )
										}
									>
										{ icon.icon ?? icon.value }
									</Button>
								) ) }
							</Grid>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={ __(
									'Show icon on left',
									'acf-blocks-toolkit'
								) }
								checked={ iconPositionLeft }
								onChange={ () => {
									setAttributes( {
										iconPositionLeft: ! iconPositionLeft,
									} );
								} }
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
	addInspectorControls
);

/**
 * Add icon and position classes in the Editor.
 *
 * @since 0.1.0
 * @param {Object} BlockListBlock
 */
function addClasses( BlockListBlock ) {
	return ( props ) => {
		const { name, attributes } = props;

		if ( 'core/button' !== name || ! attributes?.icon ) {
			return <BlockListBlock { ...props } />;
		}

		const classes = classnames( props?.className, {
			[ `has-icon__${ attributes?.icon }` ]: attributes?.icon,
			'has-icon-position__left': attributes?.iconPositionLeft,
		} );

		return <BlockListBlock { ...props } className={ classes } />;
	};
}

addFilter(
	'editor.BlockListBlock',
	'acf-blocks-toolkit/add-classes',
	addClasses
);
