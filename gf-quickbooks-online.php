<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             0.1.0
 * @package           GFQuickbooksOnline
 *
 * @wordpress-plugin
 * Plugin Name:       GF Quickbooks Online
 * Plugin URI:        http://example.com/gf-quickbooks-online-uri/
 * Description:       Allows Gravity Forms form Feeds to be sent to Quickbooks Online accounts
 * Version:           0.1.0
 * Author:            Stuart Laverick
 * Author URI:        http://www.appropriatesolutions.co.uk/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gf-quickbooks-online
 * Domain Path:       /languages
 */
namespace AppSol\GFQuickbooksOnline;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

define('GF_QUICKBOOKS_ONLINE_VERSION', '0.1.0');
define('GF_QUICKBOOKS_ONLINE_PATH', __FILE__);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gf-quickbooks-online-activator.php
 */
function activateGFQuickbooksOnline()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-gf-quickbooks-online-activator.php';
    Includes\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gf-quickbooks-online-deactivator.php
 */
function deactivateGFQuickbooksOnline()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-gf-quickbooks-online-deactivator.php';
    Includes\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activateGFQuickbooksOnline');
register_deactivation_hook(__FILE__, 'deactivateGFQuickbooksOnline');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-gf-quickbooks-online.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function runGFQuickbooksOnline()
{
    $plugin = new Includes\GFQuickbooksOnline();
    $plugin->run();
}
runGFQuickbooksOnline();
