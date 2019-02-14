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
	 * Activate the plugin.
	 *
	 * Here we run the activation setup of the plugin as well as register any needed CRON jobs.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		register_activation_hook( __FILE__, 'dailybrief_activation' );
	}

	/**
	 * Activate the plugin.
	 */
	function dailybrief_activation() {
		if ( add_action( 'dailybrief_daily_event', 'dailybrief_do_daily_event' ) ) {
			if ( ! wp_next_scheduled( 'dailybrief_daily_event' ) ) {
				// Lets schedule the next brief for tomorrow after midnight according to this sites Timezone.
				try {
					$date = new DateTime( 'tomorrow', new DateTimeZone( get_option( 'timezone_string' ) ) );
				} catch ( Exception $e ) {
					wp_die( $e->getMessage(), 'DailyBrief Exploded' );
				}
				$timestamp = $date->getTimestamp();
				wp_schedule_event( $timestamp, 'daily', 'dailybrief_daily_event' );
			}
		} else {
			$error = new WP_Error( 'error', 'No Cron Event Added' );
		}
	}

	/**
	 * Do daily CRON job.
	 */
	function dailybrief_do_daily_event() {
		// do brief every day!
		$dc = new Dailybrief();
		$dc->update_globals();
		$options = $dc->get_options();
		// Generate post.
		$dailybrief = $dc->create(
			array(
				'preview' => false,
				'period'  => $options['period'],
				'start'   => date( 'Y-m-d', strtotime( $options['start_date'] ) ),
				'end'     => date( 'Y-m-d', strtotime( $options['end_date'] ) ),
				'post'    => true,
				'publish' => $options['cron_publish'],
			)
		);
	}

}
