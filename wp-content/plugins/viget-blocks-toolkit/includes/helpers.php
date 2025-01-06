<?php
/**
 * Helper functions
 *
 * @package VigetBlocksToolkit
 */

use Viget\VigetBlocksToolkit\Core;

if ( ! function_exists( 'vgtbt' ) ) {
	/**
	 * Viget Blocks Toolkit Core API instance.
	 *
	 * @return Core
	 */
	function vgtbt(): Core {
		return Core::instance();
	}
}

if ( ! function_exists( 'block_attrs' ) ) {
	/**
	 * Render the block attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $block
	 * @param string $custom_class
	 * @param array  $attrs
	 */
	function block_attrs( array $block, string $custom_class = '', array $attrs = [] ): void {
		$id = ! empty( $attrs['id'] ) ? $attrs['id'] : get_block_id( $block );
		$id = apply_filters( 'vgtbt_block_id_attr', $id, $block );

		if ( is_admin() ) {
			if ( ! empty( $block['anchor'] ) ) {
				$attrs['data-id'] = $block['anchor'];
			} else {
				$attrs['data-id'] = $id;
			}
		} else {
			$attrs['id'] = $id;
		}

		$block_class = get_block_class( $block, $custom_class );

		if ( ! empty( $attrs['class'] ) ) {
			$attrs['class'] .= ' ' . $block_class;
		} else {
			$attrs['class'] = $block_class;
		}

		$block_styles = ! is_admin() ? get_core_styles( $block ) : '';
		if ( ! empty( $attrs['style'] ) ) {
			$attrs['style'] .= $block_styles;
		} else {
			$attrs['style'] = $block_styles;
		}

		if ( ! array_key_exists( 'data-supports-jsx', $attrs ) && ! empty( $block['supports']['jsx'] ) ) {
			$attrs['data-supports-jsx'] = 'true';
		}

		$attrs = apply_filters( 'vgtbt_block_attrs', $attrs, $block );

		// Prepare Extra attributes.
		$extra = [
			'class' => $attrs['class'],
			'style' => $attrs['style'],
		];

		if ( ! is_preview() ) {
			unset( $attrs['class'] );
			unset( $attrs['style'] );
		}

		if ( ! empty( $attrs['id'] ) ) {
			$extra['id'] = $attrs['id'];
			unset( $attrs['id'] );
		}

		foreach ( $attrs as $key => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}
			echo ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		echo ' '; // Prep for additional block_attrs.

		do_action( 'vgtbt_block_attr', $block );

		echo wp_kses_data( get_block_wrapper_attributes( $extra ) );
	}
}

if ( ! function_exists( 'get_block_id' ) ) {
	/**
	 * Get the block ID attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param array $block
	 * @param bool $ignore_anchor
	 *
	 * @return string
	 */
	function get_block_id( array $block, bool $ignore_anchor = false ): string {
		if ( ! empty( $block['anchor'] ) && ! $ignore_anchor ) {
			$id = $block['anchor'];
		} else {
			$prefix = str_replace( 'acf/', '', $block['name'] );
			if ( empty( $block['id'] ) ) {
				$block['id'] = uniqid();
			}
			$id = $prefix . '_' . $block['id'];
		}

		return apply_filters( 'vgtbt_block_id', $id, $block );
	}
}

if ( ! function_exists( 'get_block_class' ) ) {
	/**
	 * Get the block class attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param array $block
	 * @param string $custom_class
	 *
	 * @return string
	 */
	function get_block_class( array $block, string $custom_class = '' ): string {
		$classes = [
			'wp-block',
			'acf-block',
			'acf-block-' . str_replace( 'acf/', '', $block['name'] ),
		];

		$core_classes = get_core_classes( $block );

		if ( $core_classes ) {
			$classes = array_merge( $classes, $core_classes );
		}

		if ( ! empty( $block['className'] ) ) {
			$classes[] = $block['className'];
		}

		if ( ! empty( $custom_class ) ) {
			$classes[] = $custom_class;
		}

		if ( ! empty( $block['align'] ) ) {
			$classes[] = 'align' . $block['align'];
		}

		if ( ! empty( $block['data']['limit_visibility'] ) ) {
			$classes[] = 'vgtbt-limit-visibility';
		}

		return apply_filters( 'vgtbt_block_class', implode( ' ', $classes ), $block );
	}
}

if ( ! function_exists( 'vgtbt_render_block' ) ) {
	/**
	 * Render an ACF block with specific properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_name
	 * @param array $props
	 */
	function vgtbt_render_block( string $block_name, array $props = [] ): void {
		if ( ! str_starts_with( $block_name, 'acf/' ) ) {
			$block_name = 'acf/' . $block_name;
		}

		$block = array_merge(
			[
				'name' => $block_name,
			],
			$props
		);

		if ( empty( $block['id'] ) ) {
			$block['id'] = uniqid();
		}

		if ( ! function_exists( 'acf_render_block' ) ) {
			render_block( $block );
		} else {
			acf_render_block( $block );
		}
	}
}

if ( ! function_exists( 'get_block_from_blocks' ) ) {
	/**
	 * Retrieves the first instance of the specified block type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of block to retrieve.
	 * @param array $blocks Array of blocks to search through.
	 *
	 * @return array|false
	 */
	function get_block_from_blocks( string $name, array $blocks ): array|false {
		foreach ( $blocks as $block ) {
			if ( $name === $block['blockName'] ) {
				return $block;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$result = get_block_from_blocks( $name, $block['innerBlocks'] );

				if ( $result ) {
					return $result;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'get_block_fields' ) ) {
	/**
	 * Get fields for a block
	 *
	 * @param string $block_name
	 *
	 * @return array
	 */
	function get_block_fields( string $block_name ): array {
		if ( ! str_starts_with( $block_name, 'acf/' ) ) {
			$block_name = 'acf/' . $block_name;
		}

		$field_groups = acf_get_field_groups();
		$fields       = [];

		foreach ( $field_groups as $field_group ) {
			foreach ( $field_group['location'] as $locations ) {
				foreach ( $locations as $location ) {
					if ( empty( $location['operator'] ) || '==' !== $location['operator'] || empty( $location['param'] ) || 'block' !== $location['param'] ) {
						continue;
					}

					if ( ! in_array( $location['value'], [ 'all', $block_name ], true ) ) {
						continue;
					}

					// Fields may not be loaded yet.
					if ( empty( $field_group['fields'] ) ) {
						$group  = json_decode( file_get_contents( $field_group['local_file'] ), true );
						$fields = array_merge( $fields, $group['fields'] );
					} else {
						$fields = array_merge( $fields, $field_group['fields'] );
					}
				}
			}
		}

		return $fields;
	}
}

if ( ! function_exists( 'get_field_property' ) ) {
	/**
	 * Get a property from a field.
	 *
	 * @param string $selector
	 * @param string $property
	 * @param string|null $group_id
	 *
	 * @return string
	 */
	function get_field_property( string $selector, string $property, string $group_id = null ): string {
		if ( null !== $group_id ) {
			$fields = acf_get_fields( $group_id );
			foreach ( $fields as $field_array ) {
				if ( $selector === $field_array['name'] ) {
					$field = $field_array;
					break;
				}
			}
		} else {
			$field = get_field_object( $selector );
		}

		if ( ! $field || ! is_array( $field ) ) {
			return '';
		}

		if ( empty( $field[ $property ] ) ) {
			return '';
		}

		return $field[ $property ];
	}
}

if ( ! function_exists( 'inner_blocks' ) ) {
	/**
	 * Escape and encode ACF Block InnerBlocks template and allowed blocks
	 *
	 * @since 1.0.0
	 *
	 * @param array $props {
	 *    @type array  $allowedBlocks Allowed blocks
	 *    @type array  $template      Block Template
	 *    @type string $templateLock  Template Lock
	 *    @type string $className     Class Name
	 * }
	 *
	 * @return void
	 */
	function inner_blocks( array $props = [] ): void {
		$json_encode = [ 'allowedBlocks', 'template' ];
		$attributes  = '';

		foreach ( $props as $attr => $value ) {
			$attr_value  = in_array( $attr, $json_encode, true ) ? wp_json_encode( $value ) : $value;
			$attributes .= ' ' . esc_attr( $attr ) . '="' . esc_attr( $attr_value ) . '"';
		}

		printf(
			'<InnerBlocks%s />',
			$attributes
		);
	}
}

if ( ! function_exists( 'print_admin_message' ) ) {
	/**
	 * Output notice to admins only in Block Editor
	 *
	 * @since 1.0.0
	 *
	 * @param string $notice
	 * @param string $class
	 *
	 * @return void
	 */
	function print_admin_message( string $notice = '', string $class = 'vgtbt-admin-message' ): void {
		if ( ! is_admin() || ! $notice ) {
			return;
		}

		printf(
			'<div class="%s"><p style="padding: 3em; text-align: center;">%s</p></div>',
			esc_attr( $class ),
			nl2br( esc_html( $notice ) )
		);
	}
}

if ( ! function_exists( 'is_acf_saving_field' ) ) {
	/**
	 * Check if the current screen is an ACF edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function is_acf_saving_field(): bool {
		global $pagenow;

		if ( doing_action( 'acf/update_field_group' ) ) {
			return true;
		}

		if ( 'post.php' !== $pagenow ) {
			return false;
		}

		if ( empty( $_GET['post'] ) || empty( $_GET['action'] ) ) {
			return false;
		}

		$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) );
		$action  = sanitize_text_field( wp_unslash( $_GET['action'] ) );

		if ( 'edit' === $action && 'acf-field-group' === get_post_type( $post_id ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'get_core_classes' ) ) {
	/**
	 * Get core block classes
	 *
	 * @param array $block
	 *
	 * @return array
	 */
	function get_core_classes( array $block ): array {
		$classes = [];

		if ( ! empty( $block['backgroundColor'] ) ) {
			$classes[] = 'has-' . $block['backgroundColor'] . '-background-color';
			$classes[] = 'has-background';
		} elseif ( ! empty( $block['attributes']['backgroundColor']['default'] ) ) {
			$classes[] = 'has-' . $block['attributes']['backgroundColor']['default'] . '-background-color';
			$classes[] = 'has-background';
		}

		if ( ! empty( $block['textColor'] ) ) {
			$classes[] = 'has-' . $block['textColor'] . '-color';
			$classes[] = 'has-text-color';
		} elseif ( ! empty( $block['attributes']['textColor']['default'] ) ) {
			$classes[] = 'has-' . $block['attributes']['textColor']['default'] . '-color';
			$classes[] = 'has-text-color';
		}

		return $classes;
	}
}

if ( ! function_exists( 'get_core_styles' ) ) {
	/**
	 * Get core block styles
	 *
	 * @param array $block
	 *
	 * @return string
	 */
	function get_core_styles( array $block ): string {
		if ( ! empty( $block['style'] ) ) {
			$styles = wp_style_engine_get_styles( $block['style'] );
			return $styles['css'];
		}

		return '';
	}
}
