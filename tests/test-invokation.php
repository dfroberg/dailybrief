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
	 * Create posts.
	 */
	function test_wp_count_posts_insert_invalidation() {
		$post_ids             = $this->factory->post->create_many( 10 );
		$initial_counts       = wp_count_posts();
		$key                  = array_rand( $post_ids );
		$_post                = get_post( $post_ids[ $key ], ARRAY_A );
		$_post['post_status'] = 'draft';
		wp_insert_post( $_post );
		$post = get_post( $post_ids[ $key ] );
		$this->assertEquals( 'draft', $post->post_status );
		$this->assertNotEquals( 'publish', $post->post_status );

		$after_draft_counts = wp_count_posts();
		$this->assertEquals( 1, $after_draft_counts->draft );
		$this->assertEquals( 9, $after_draft_counts->publish );
		$this->assertNotEquals( $initial_counts->publish, $after_draft_counts->publish );
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
		$dc->set_post_category( 1 );
		$dc->set_author_id( 1 );
		$options = $dc->get_options();
		$dc->create(
			array(
				'preview'      => false,
				'period'       => 'day',
				'days'         => date( 'Y-m-d' ),
				'use-excerpts' => $options['use_excerpts'],
				'post'         => true,
				'publish'      => true,
			)
		);
		// Pass test.
		$this->assertTrue( true );
	}
	/**
	 * Load plugin.
	 */
	public function test_find_post_dailybrief() {
		$this->assertTrue( true );
	}
}
