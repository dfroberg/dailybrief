<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.froberg.org
 * @since             1.0.0
 * @package           Dailybrief
 *
 * @wordpress-plugin
 * Plugin Name:       Dailybrief
 * Plugin URI:        https://github.com/dfroberg/dailybrief
 * Description:       WordPress plugin with WP-CLI support to generate a daily brief of a previous day or periods posts.
 * Version:           1.0.40
 * Author:            Daniel Froberg
 * Author URI:        https://www.froberg.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dailybrief
 * Domain Path:       /languages
 * GitLab Plugin URI: https://gitlab.froberg.org/dfroberg/dailybrief
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DAILYBRIEF_VERSION', '1.0.40' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dailybrief-cli-command.php';
}

register_activation_hook( __FILE__, array( Dailybrief::class, 'activator' ) );
register_deactivation_hook( __FILE__, array( Dailybrief::class, 'deactivator' ) );

define( 'DAILYBRIEF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Check if SteemPress is Installed.
 */
if ( ! class_exists( 'Steempress_sp_Admin' ) ) {
	define( 'DAILYBRIEF_DETECTED_STEEMPRESS', true );
}

/**
 * Helper class for getting correct Timezone in WordPress.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpdatetimezone.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dailybrief.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dailybrief() {

	$plugin = new Dailybrief();
	$plugin->run();

}

run_dailybrief();
