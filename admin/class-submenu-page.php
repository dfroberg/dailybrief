<?php
/**
 * Creates the submenu page for the plugin.
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/admin
 * @author     Daniel Froberg <danny@froberg.org>
 */

/**
 * Creates the submenu page for the plugin.
 *
 * Provides the functionality necessary for rendering the page corresponding
 * to the submenu with which this page is associated.
 *
 * @package Custom_Admin_Settings
 */
class Submenu_Page {

	/**
	 * This function renders the contents of the page associated with the Submenu
	 * that invokes the render method. In the context of this plugin, this is the
	 * Submenu class.
	 */
	public function render() {
		echo 'This is dailybrief admin page.';
	}
}
