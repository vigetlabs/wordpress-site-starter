<?php
/**
 * Cache Class
 *
 * @package ACFFormBlocks
 */

namespace ACFFormBlocks;

/**
 * Class for Caching
 */
class Cache {

	/**
	 * The storage.
	 *
	 * @var array
	 */
	private static array $storage = [];

	/**
	 * Get a cached value.
	 *
	 * @param string $key The key.
	 *
	 * @return mixed
	 */
	public static function get( string $key ): mixed {
		return self::$storage[ $key ] ?? null;
	}

	/**
	 * Set a cached value.
	 *
	 * @param string $key The key.
	 * @param mixed  $value The value.
	 *
	 * @return void
	 */
	public static function set( string $key, mixed $value ): void {
		self::$storage[ $key ] = $value;
	}
}
