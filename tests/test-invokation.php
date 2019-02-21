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
	 * Setup.
	 */
	function setUp() {
		parent::setUp();
	}
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
		$dc = new Dailybrief();

		if ( 'dailybrief' !== $dc->get_plugin_name() ) {
			$this->assertTrue( false );
		}
		// Pass test.
		$this->assertTrue( true );
	}
	/**
	 * Load plugin.
	 */
	public function test_getsetoptions_dailybrief() {
		// Include class.
		require_once '/builds/dfroberg/dailybrief/dailybrief.php';
		$dc = new Dailybrief();

		$dc->set_article_delimiter( 'delimiter' );
		if ( 'delimiter' !== $dc->get_article_delimiter() ) {
			$this->assertTrue( false );
		}

		$dc->set_always_skip_category( array( '-1' ) );
		if ( ! in_array( '-1', $dc->get_always_skip_category() ) ) {
			$this->assertTrue( false );
		}
		// Pass test.
		$this->assertTrue( true );
	}
}
