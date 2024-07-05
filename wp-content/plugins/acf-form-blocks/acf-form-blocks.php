<?php
/**
 * Plugin Name:       ACF Form Blocks
 * Plugin URI:        https://viget.com
 * Description:       Form blocks using ACF.
 * Version:           1.0.0
 * Requires at least: 5.7
 * Requires PHP:      8.1
 * Requires Plugins:  advanced-custom-fields-pro, acf-blocks-toolkit
 * Author:            Viget
 * Author URI:        https://viget.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acf-form-blocks
 * Domain Path:       /languages
 *
 * @package ACFFormBlocks
 */

// Plugin version.
const ACFFB_VERSION = '1.0.0';

// Plugin path.
define( 'ACFFB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Plugin URL.
define( 'ACFFB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Maybe Load Composer dependencies.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Load include files.
$plugin_files = glob( ACFFB_PLUGIN_PATH . 'includes/*.php' );

foreach ( $plugin_files as $path ) {
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
