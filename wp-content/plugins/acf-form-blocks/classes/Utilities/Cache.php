<?php
/**
 * Cache Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks\Utilities;

use ACFFormBlocks\Form;

/**
 * Class for Caching
 */
class Cache {

	/**
	 * The storage.
	 *
	 * @var Form[]
	 */
	private static array $storage = [];

	/**
	 * Get a cached value.
	 *
	 * @param string $key The key.
	 *
	 * @return ?Form
	 */
	public static function get( string $key ): ?Form {
		return self::$storage[ $key ] ?? null;
	}

	/**
	 * Set a cached value.
	 *
	 * @param string $key The key.
	 * @param Form   $form The form.
	 *
	 * @return void
	 */
	public static function set( string $key, Form $form ): void {
		self::$storage[ $key ] = $form;
	}

	/**
	 * Find a cached Form.
	 *
	 * @param string $form_id The form ID.
	 *
	 * @return ?Form
	 */
	public static function find( string $form_id ): ?Form {
		foreach ( self::$storage as $cache ) {
			$block_name = str_replace( '/', '_', $cache->get_form_element()['name'] );
			$form_id    = $block_name . '_' . $form_id;

			if ( $cache->get_form_object()->get_id() === $form_id ) {
				return $cache;
			}
		}

		return null;
	}
}
