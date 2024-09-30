<?php
/**
 * Plugin Name:       ACF Blocks Toolkit
 * Plugin URI:        https://viget.com
 * Description:       ACF Block Registration and Helper functions.
 * Version:           1.0.2
 * Requires at least: 5.7
 * Requires PHP:      8.1
 * Requires Plugins:  advanced-custom-fields-pro
 * Author:            Viget
 * Author URI:        https://viget.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       acf-blocks-toolkit
 * Domain Path:       /languages
 *
 * @package ACFBlocksToolkit
 */

use Viget\ACFBlocksToolkit\BlockIcons;
use Viget\ACFBlocksToolkit\BlockRegistration;
use Viget\ACFBlocksToolkit\Settings;

// Plugin version.
const ACFBT_VERSION = '1.0.2';

// Plugin path.
define( 'ACFBT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Plugin URL.
define( 'ACFBT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Helper functions.
require_once 'includes/helpers.php';

// Timber functions.
require_once 'includes/timber.php';

// Assets.
require_once 'includes/assets.php';

// Block Registration class.
require_once 'src/classes/BlockRegistration.php';

// Block Settings class.
require_once 'src/classes/Settings.php';

// Block Icons support.
require_once 'src/classes/BlockIcons.php';

BlockRegistration::init();
Settings::init();
new BlockIcons();
