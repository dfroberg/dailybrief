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
	public function test_instantiate_dailybrief() {
		// Test if WPINC is defined.
		// If this file is called directly, abort.
		if ( ! defined( 'WPINC' ) ) {
			$this->assertTrue( true ); // Bail!
		}
		// Include class.
		require_once '/builds/dfroberg/dailybrief/dailybrief.php';

		if ( ! defined( DAILYBRIEF_VERSION ) ) {
			$this->assertTrue( false );
		}
		// Pass test.
		$this->assertTrue( true );
	}
}
