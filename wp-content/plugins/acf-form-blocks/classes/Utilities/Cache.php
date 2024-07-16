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
		if ( ! str_starts_with( $key, 'acf_form_' ) ) {
			$key = 'acf_form_' . $key;
		}

		return self::$storage[ $key ] ?? null;
	}

	/**
	 * Set a cached value.
	 *
	 * @param string $key The key.
	 * @param Form   $form The form.
	 * @param bool   $new  Is this a new form?
	 *
	 * @return void
	 */
	public static function set( string $key, Form $form, bool $new = false ): void {
		if ( ! str_starts_with( $key, 'acf_form_' ) ) {
			$key = 'acf_form_' . $key;
		}

		if ( $new && ! empty( self::$storage[ $key ] ) ) {
			// Not sure why this would ever happen, but it does.
			return;
		}

		self::$storage[ $key ] = $form;
	}
}
