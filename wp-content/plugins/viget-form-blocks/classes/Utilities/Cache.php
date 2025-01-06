<?php
/**
 * Cache Class
 *
 * @package VigetFormBlocks
 */

namespace VigetFormBlocks\Utilities;

use VigetFormBlocks\Form;

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
		$key = Form::prefix_id( $key );
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
		$key = Form::prefix_id( $key );

		if ( $new && ! empty( self::$storage[ $key ] ) ) {
			// Not sure why this would ever happen, but it does.
			return;
		}

		self::$storage[ $key ] = $form;
	}
}
