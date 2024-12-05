import {
    ToggleControl,
    PanelBody,
    __experimentalNumberControl as NumberControl,
    SelectControl,
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
    Panel
} from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

const excludeBlocks = [
    'core/rss',
];

/**
 * Add breakpoint visibility controls to block
 */
const withBreakpointVisibility = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (excludeBlocks.includes(props.name) || !props.attributes) {
            return <BlockEdit {...props} />;
        }

        const { attributes, setAttributes } = props;
        const [isPanelOpen, setIsPanelOpen] = useState(false);

        // Define our visibility attributes with defaults
        const visibility = attributes.breakpointVisibility || {
            useCustom: false,
            desktop: false,
            tablet: false,
            mobile: false,
            customBreakpoint: {
                width: '768',
                unit: 'px',
                action: 'hide',
                mobileFirst: false
            }
        };

        const updateVisibility = (key, value) => {
            setAttributes({
                breakpointVisibility: {
                    ...visibility,
                    [key]: value,
                },
            });
        };

        const updateCustomBreakpoint = (key, value) => {
            setAttributes({
                breakpointVisibility: {
                    ...visibility,
                    customBreakpoint: {
                        ...visibility.customBreakpoint,
                        [key]: value
                    }
                },
            });
        };

        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody
                        title={__('Responsive', 'acf-blocks-toolkit')}
                        opened={isPanelOpen}
                        onToggle={() => setIsPanelOpen(!isPanelOpen)}
                    >
                        <ToggleControl
                            label={__('Hide on Desktop', 'acf-blocks-toolkit')}
                            checked={visibility.desktop}
                            onChange={(value) => updateVisibility('desktop', value)}
                            disabled={visibility.useCustom}
                        />
                        <ToggleControl
                            label={__('Hide on Tablet', 'acf-blocks-toolkit')}
                            checked={visibility.tablet}
                            onChange={(value) => updateVisibility('tablet', value)}
                            disabled={visibility.useCustom}
                        />
                        <ToggleControl
                            label={__('Hide on Mobile', 'acf-blocks-toolkit')}
                            checked={visibility.mobile}
                            onChange={(value) => updateVisibility('mobile', value)}
                            disabled={visibility.useCustom}
                        />

                        <hr />

                        <ToggleControl
                            label={__('Use Custom Breakpoint', 'acf-blocks-toolkit')}
                            checked={visibility.useCustom}
                            onChange={(value) => updateVisibility('useCustom', value)}
                        />

                        {visibility.useCustom && (
                            <>
                                <div style={{
                                    display: 'grid',
                                    gridTemplateColumns: '2fr 1fr',
                                    gap: '8px',
                                    alignItems: 'start'
                                }}>
                                    <NumberControl
                                        label={__('Breakpoint Width', 'acf-blocks-toolkit')}
                                        value={visibility.customBreakpoint.width}
                                        onChange={(value) => updateCustomBreakpoint('width', value)}
                                        min={0}
                                        step={1}
                                    />
                                    <SelectControl
                                        label={__('Unit', 'acf-blocks-toolkit')}
                                        value={visibility.customBreakpoint.unit}
                                        options={[
                                            { label: 'px', value: 'px' },
                                            { label: '%', value: '%' },
                                            { label: 'rem', value: 'rem' },
                                            { label: 'vw', value: 'vw' },
                                            { label: 'vh', value: 'vh' },
                                        ]}
                                        onChange={(value) => updateCustomBreakpoint('unit', value)}
                                    />
                                </div>
                                <ToggleGroupControl
                                    label={__('Visibility Action', 'acf-blocks-toolkit')}
                                    value={visibility.customBreakpoint.action}
                                    onChange={(value) => updateCustomBreakpoint('action', value)}
                                    isBlock
                                >
                                    <ToggleGroupControlOption value="show" label={__('Show', 'acf-blocks-toolkit')} />
                                    <ToggleGroupControlOption value="hide" label={__('Hide', 'acf-blocks-toolkit')} />
                                </ToggleGroupControl>
                                <ToggleControl
                                    label={__('Mobile First', 'acf-blocks-toolkit')}
                                    help={__('When enabled, applies to screens smaller than breakpoint', 'acf-blocks-toolkit')}
                                    checked={visibility.customBreakpoint.mobileFirst}
                                    onChange={(value) => updateCustomBreakpoint('mobileFirst', value)}
                                />
                            </>
                        )}
                    </PanelBody>
                </InspectorControls>
            </>
        );
    };
}, 'withBreakpointVisibility');

/**
 * Add visibility attributes to blocks
 */
addFilter(
    'blocks.registerBlockType',
    'acf-blocks-toolkit/breakpoint-visibility-attributes',
    (settings) => {
        if (excludeBlocks.includes(settings.name) || !settings.attributes) {
            return settings;
        }

        settings.attributes.breakpointVisibility = {
            type: 'object',
            default: {
                useCustom: false,
                desktop: false,
                tablet: false,
                mobile: false,
                customBreakpoint: {
                    width: '768',
                    unit: 'px',
                    action: 'hide',
                    mobileFirst: false
                }
            }
        };
        return settings;
    }
);

// Apply the breakpoint visibility to all blocks
addFilter(
    'editor.BlockEdit',
    'acf-blocks-toolkit/with-breakpoint-visibility',
    withBreakpointVisibility
);

/**
 * Add visibility attributes to block wrapper
 */
addFilter(
    'blocks.getSaveContent.extraProps',
    'acf-blocks-toolkit/breakpoint-visibility-attributes',
    (extraProps, blockType, attributes) => {
        if (!attributes.breakpointVisibility) {
            return extraProps;
        }

        const { useCustom, desktop, tablet, mobile } = attributes.breakpointVisibility;

        if (!useCustom) {
            if (desktop) extraProps['data-visibility-desktop'] = 'hide';
            if (tablet) extraProps['data-visibility-tablet'] = 'hide';
            if (mobile) extraProps['data-visibility-mobile'] = 'hide';
        }
        return extraProps;
    }
);
