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
		// Include class.
		require dirname( plugin_basename( __FILE__ ) ) . 'dailybrief.php';
		if ( ! defined( DAILYBRIEF_VERSION ) ) {
			$this->assertTrue( false );
		}
		// Pass test.
		$this->assertTrue( true );
	}
}
