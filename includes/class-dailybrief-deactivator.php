<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://www.froberg.org
 * @since      1.0.0
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 * @author     Daniel Froberg <danny@froberg.org>
 */
class Dailybrief_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		register_deactivation_hook( __FILE__, 'dailybrief_deactivation' );
	}


	/**
	 * Clean up after deactication & de-register any CRON jobs.
	 */
	function dailybrief_deactivation() {
		wp_clear_scheduled_hook( 'dailybrief_daily_event' );
	}
}
