<?php
/**
 * ACF Block Settings
 *
 * @package VigetBlocksToolkit
 */

namespace Viget\VigetBlocksToolkit;

/**
 * Settings Class
 */
class Settings {

	/**
	 * @var array
	 */
	public static array $paths = [];

	/**
	 * Load block JSON from block folders.
	 */
	public static function init(): void {
		self::register_paths();
		self::save_paths();
		self::set_google_maps_api_key();
	}

	/**
	 * Load paths for ACF JSON
	 *
	 * @return void
	 */
	public static function register_paths(): void {
		add_filter(
			'acf/json/load_paths',
			function ( array $paths ): array {
				if ( ! empty( self::$paths ) ) {
					return array_merge( $paths, self::$paths );
				}

				foreach ( BlockRegistration::get_all_blocks() as $block ) {
					if ( empty( $block['path'] ) ) {
						continue;
					}

					self::$paths[] = $block['path'];
				}

				return array_merge( $paths, self::$paths );
			}
		);
	}

	/**
	 * Set Save paths for ACF JSON
	 *
	 * @return void
	 */
	public static function save_paths(): void {
		/**
		 * Save block fields in block folder.
		 *
		 * @param string $path Path where to save JSON.
		 *
		 * @return string
		 */
		add_filter(
			'acf/settings/save_json',
			function ( $path ) {
				$block = self::get_posted_block();

				if ( ! $block ) {
					return $path;
				}

				$block_path = BlockRegistration::get_block_location( $block );

				if ( $block_path ) {
					return $block_path;
				}

				return $path;
			}
		);
	}

	/**
	 * Grab the block name from posted field settings, if present.
	 *
	 * @return false|string
	 */
	public static function get_posted_block(): false|string {
		if ( empty( $_POST['acf_field_group'] ) || ! is_array( $_POST['acf_field_group'] ) || empty( $_POST['acf_field_group']['location'] ) ) {
			return false;
		}

		if ( count( $_POST['acf_field_group']['location'] ) > 1 ) {
			return false;
		}

		foreach ( $_POST['acf_field_group']['location'] as $group_key => $group ) {
			if ( empty( $group ) ) {
				continue;
			}

			if ( count( $group ) > 1 ) {
				return false;
			}

			foreach ( $group as $rule ) {
				if ( empty( $rule ) || empty( $rule['param'] ) || empty( $rule['value'] ) ) {
					continue;
				}

				if ( 'block' === $rule['param'] && '==' === $rule['operator'] && ! empty( $rule['value'] ) ) {
					return str_replace( 'acf/', '', $rule['value'] );
				}
			}
		}

		return false;
	}

	/**
	 * Set Google Maps API Key
	 *
	 * @return void
	 */
	public static function set_google_maps_api_key(): void {
		/**
		 * Add Google Maps API Key if present.
		 *
		 * @param array $api API Array.
		 *
		 * @return array
		 */
		add_filter(
			'acf/fields/google_map/api',
			function ( array $api ): array {
				$key = get_theme_mod( 'google_maps_api_key' );

				if ( $key ) {
					$api['key'] = $key;
				}

				return $api;
			}
		);

		/**
		 * Update Google API Key ACF Setting.
		 */
		add_action(
			'acf/init',
			function () {
				$key = get_theme_mod( 'google_maps_api_key' );

				if ( $key ) {
					acf_update_setting( 'google_api_key', $key );
				}
			}
		);

	}
}
