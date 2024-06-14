<?php
/**
 * Plugin Name:       Viget Parts Kit
 * Plugin URI:        https://viget.com
 * Description:       Component Parts Kit integration for WordPress made by Viget.
 * Version:           1.0.0
 * Requires at least: 5.7
 * Requires PHP:      8.1
 * Author:            Viget
 * Author URI:        https://viget.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       viget-parts-kit
 * Domain Path:       /languages
 *
 * @package VigetPartsKit
 */

// Plugin version.
const VPK_VERSION = '1.0.0';

// Plugin file.
const VPK_PLUGIN_FILE = __FILE__;

// Plugin path.
define( 'VPK_PLUGIN_PATH', plugin_dir_path( VPK_PLUGIN_FILE ) );

// Plugin URL.
define( 'VPK_PLUGIN_URL', plugin_dir_url( VPK_PLUGIN_FILE ) );

// Include the PartsKit classes.
require_once VPK_PLUGIN_PATH . 'src/classes/WPGutenberg.php';
require_once VPK_PLUGIN_PATH . 'src/classes/PartsKit.php';
require_once VPK_PLUGIN_PATH . 'src/classes/BlockParts.php';

// Init the Parts Kit.
add_action(
	'plugins_loaded',
	function () {
		new VigetPartsKit\PartsKit();
		new VigetPartsKit\BlockParts();
	}
);
