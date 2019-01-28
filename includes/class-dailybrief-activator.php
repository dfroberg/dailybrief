<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.froberg.org
 * @since      1.0.0
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 * @author     Daniel Froberg <danny@froberg.org>
 */
class Dailybrief_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        register_activation_hook(__FILE__, 'dailybrief_activation');
        add_action('dailybrief_daily_event', 'dailybrief_do_daily_event');
	}

    function dailybrief_activation() {
        if (! wp_next_scheduled ( 'dailybrief_daily_event' )) {
            wp_schedule_event(time(), 'daily', 'dailybrief_daily_event');
        }
    }

    function dailybrief_do_daily_event() {
        // do brief every day
    }

}
