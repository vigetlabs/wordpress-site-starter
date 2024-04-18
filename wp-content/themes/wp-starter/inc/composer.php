<?php
/**
 * Composer Autoload
 *
 * @package VigetThemeBoilerplate
 */

// Require Composer autoloader if it exists.
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require_once get_template_directory() . '/vendor/autoload.php';
}

/** Add lowercase file/folder and class-prefix autoload support */
if ( file_exists( get_template_directory() . '/vendor/composer/autoload_classmap.php' ) ) {
	$class_map = require get_template_directory() . '/vendor/composer/autoload_classmap.php';

	spl_autoload_register(
		function ($class) use ($class_map) {
			if (isset($class_map[$class])) {
				if (file_exists($class_map[$class])) {
					require_once $class_map[$class];
					return true;
				}

				// Convert directories from CamelCase to kebab-case.
				$path = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $class_map[$class]));
				if (file_exists($path)) {
					require_once $path;
					return true;
				}

				// Finally, prepend with class-
				$prefixed = dirname($path) . '/class-' . basename($path);
				if (file_exists($prefixed)) {
					require_once $prefixed;
					return true;
				}

				return true;
			}

			return false;
		},
		true,
		true
	);
}
