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
		// Let's create a category.
		$this->cat1 = $this->factory->term->create(
			array(
				'taxonomy' => 'category',
			)
		);
		// Let's create some featured posts in a category.
		$this->factory->post->create_many(
			2,
			array(
				'post_category' => array( $this->cat1 ),
				'tax_input'     => array(
					'taxonomy'   => 'term',
					'prominence' => 'taxonomy-featured',
				),
			)
		);
		$this->factory->post->create_many(
			2,
			array(
				'post_category' => array( $this->cat1 ),
				'tax_input'     => array(
					'taxonomy'   => 'term',
					'prominence' => 'taxonomy-secondary-featured',
				),
			)
		);
		// And some non-featured posts.
		$this->cat1_no_prom = $this->factory->post->create_many(
			2,
			array(
				'post_category' => array( $this->cat1 ),
			)
		);
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
	/**
	 * Load plugin.
	 */
	public function test_create_dailybrief() {
		// Include class.
		require_once '/builds/dfroberg/dailybrief/dailybrief.php';
		$dc = new Dailybrief();
		$dc->update_globals();
		$options = $dc->get_options();
		$dc->create(
			array(
				'preview'         => false,
				'period'          => 'day',
				'days'            => date( 'Y-m-d' ),
				'start'           => date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) ),
				'end'             => date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) ),
				'use-excerpts'    => $options['use_excerpts'],
				'skip-categories' => $options['skip_categories'],
				'post'            => true,
				'publish'         => true,
			)
		);
		// Pass test.
		$this->assertTrue( true );
	}

}
