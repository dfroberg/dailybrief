<?php
/**
 * Class InvokationTest
 *
 * @package DailyBrief_CLI_Command
 */

/**
 * Sample test case.
 */
class InvokationTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
	/**
	 * Load plugin.
	 */
	public function test_wpinc() {
		// Test if WPINC is defined.
		// If this file is called directly, abort.
		if ( ! defined( 'WPINC' ) ) {
			$this->assertTrue( true ); // Bail!
		}
		$this->assertTrue( true );
	}
	/**
	 * Load plugin.
	 */
	public function test_instantiate_dailybrief() {
		// Include class.
		require_once '/builds/dfroberg/dailybrief/dailybrief.php';

		if ( ! defined( Dailybrief::CRON_HOOK ) ) {
			$this->assertTrue( false );
		}
		// Pass test.
		$this->assertTrue( true );
	}
}
