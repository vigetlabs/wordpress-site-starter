<?php
/**
 * Plugin Name: Viget WP
 * Plugin URI: https://github.com/vigetlabs/viget-wp
 * Description: WordPress Customization from Viget
 * Version: 1.0.0
 * Author: Viget
 * Author URI: https://www.viget.com/
 * Text Domain: viget-wp
 *
 * @package VigetWP
 */

defined( 'VIGETWP_PLUGIN_FILE' ) || define( 'VIGETWP_PLUGIN_FILE', __FILE__ );
defined( 'VIGETWP_PLUGIN_PATH' ) || define( 'VIGETWP_PLUGIN_PATH', plugin_dir_path( VIGETWP_PLUGIN_FILE ) );
defined( 'VIGETWP_PLUGIN_URL' ) || define( 'VIGETWP_PLUGIN_URL', plugin_dir_url( VIGETWP_PLUGIN_FILE ) );

/* Autoloader */
if ( file_exists( VIGETWP_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require VIGETWP_PLUGIN_PATH . 'vendor/autoload.php';
}

/* TGM plugin activation */
if ( file_exists( VIGETWP_PLUGIN_PATH . 'src/lib/class-tgm-plugin-activation.php' ) ) {
	require VIGETWP_PLUGIN_PATH . 'src/lib/class-tgm-plugin-activation.php';
}

/**
 * Initialize plugin.
 *
 * @return ?VigetWP\Core
 */
function vigetwp(): ?\VigetWP\Core {
	if ( class_exists( '\VigetWP\Core' ) ) {
		return \VigetWP\Core::get_instance();
	}

	return null;
}

vigetwp();
