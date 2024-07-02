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

// Registration functions.
require_once 'includes/helpers.php';
require_once 'includes/assets.php';
require_once 'includes/registration.php';
require_once 'includes/post-types.php';
require_once 'includes/block-fields.php';

// Form classes.
require_once 'classes/Cache.php';
require_once 'classes/Form.php';
require_once 'classes/Field.php';
require_once 'classes/Validation.php';
require_once 'classes/Submission.php';
require_once 'classes/Confirmation.php';

// Template Classes.
require_once 'classes/Template.php';
require_once 'classes/Block.php';
