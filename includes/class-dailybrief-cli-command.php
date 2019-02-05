<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.froberg.org
 * @since             1.0.0
 * @package           Dailybrief
 * @subpackage        Dailybrief/cli
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );
// Bail if WP-CLI is not present.
if ( ! defined( 'WP_CLI' ) ) {
	return;
}

// Only accessible from WP-CLI.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Class DailyBrief_CLI_Command
	 */
	class DailyBrief_CLI_Command extends WP_CLI_Command {

		/**
		 * Core DailyBrief Class
		 * Use instead of this.
		 *
		 * @var Dailybrief class Core Class
		 */
		public $dc;

		/**
		 * DailyBrief_CLI_Command constructor.
		 */
		public function __construct() {
			WP_CLI_Command::__construct();
			// constructor called when plugin loads.
			$this->dc = new Dailybrief();
			$this->dc->update_globals();
		}

		/**
		 * Sets Daily Brief options
		 *
		 * ## OPTIONS
		 * <option>
		 * : The name of the option to set i.e. header or footer
		 *
		 * <value>
		 * : The value for values that contains spaces encapsulate in single quotes.
		 *
		 * ## EXAMPLES
		 *
		 *      wp dailybrief set header '<p>This is the header, this summary contains {article_count} articles about {article_categories}.</p>'
		 *      wp dailybrief set footer '<h1>This is the footer.</h1>'
		 *      wp dailybrief set post_title 'The Your Site Daily Brief'
		 *      wp dailybrief set post_status 'draft'
		 *      wp dailybrief set post_tags 'news-blog,life,photography'
		 *
		 * @param array $args arguments.
		 * @param array $assoc_args associated arguments.
		 */
		public function set( $args, $assoc_args ) {
			$option_name  = $args[0];  // value: "arg1".
			$option_value = $args[1]; // value: 42.

			if ( ! empty( $option_name ) || ! empty( $option_value ) ) {
				$this->dc->set_option( $option_name, $option_value );
				WP_CLI::log( 'Set ' . $option_name . ' = ' . $option_value );
			}
		}

		/**
		 * Create list of posts with dates between before and after dates
		 *
		 * ## OPTIONS
		 *
		 * [--post]
		 * : Create the post in WordPress as a Draft
		 *
		 * [--publish]
		 * : Set the post_status to 'Publish' WordPress posts
		 * ---
		 * default: false
		 * ---
		 *
		 * [--use-excerpts]
		 * : Do you want to use the excepts of the summarized WordPress posts
		 * ---
		 * default: true
		 * ---
		 *
		 * [--skip-posts=<ids>]
		 * : Skip including specific posts 1,2,3,4
		 *
		 * [--skip-categories=<ids>]
		 * : Skip including specific categories, will always skip the category dailybrief posts to.
		 *
		 * [--skip-tags=<ids>]
		 * : Skip including specific tags.
		 *
		 * [--days=<days>]
		 * : Days back from where to get the posts to summarize 'today' / '-1 day' / '-2 days'
		 * ---
		 * default: today
		 * ---
		 *
		 * ### Examples:
		 * To dump an preview to the console;
		 *    wp dailybrief create --days="-1 day" --no-use-excerpts
		 *
		 * To produce a draft post;
		 *    wp dailybrief create --days="2018-10-15" --use-excerpts --post
		 *
		 * To create and publish a post;
		 *    wp dailybrief create --days="today" --post --publish
		 *
		 * @param array $args arguments.
		 * @param array $assoc_args associated arguments.
		 *
		 * @throws \WP_CLI\ExitException Throws catchable exception.
		 */
		public function create( $args, $assoc_args ) {
			global
			$wpdb;
			$days              = WP_CLI\Utils\get_flag_value( $assoc_args, 'days', 'today' );
			$today             = strtotime( $days );
			$tomorrow          = strtotime( '+1 day', $today );
			$today             = date( 'Y-m-d', $today );
			$tomorrow          = date( 'Y-m-d', $tomorrow );
			$this->dc->setDateSuffix( $today ); // used for post-title & slug suffix, contains the date it relates to.
			$before_date       = $today;
			$after_date        = $today;
			// Exclude some category ids for whatever reason and merge with the always_skip_category option.
			$skip_categories = WP_CLI\Utils\get_flag_value( $assoc_args, 'skip-categories', '' );

			// Exclude some tag ids for whatever reason and merge with the always_skip_tags option.
			$skip_tags = WP_CLI\Utils\get_flag_value( $assoc_args, 'skip-tags', '' );

			// Exclude some post_ids for whatever reason.
			$skip_posts = WP_CLI\Utils\get_flag_value( $assoc_args, 'skip-posts', '' );

			$status = array( 'publish' );
			$types  = array( 'post' );
			$buffer = false;

			// Parse some flags.
			$post = WP_CLI\Utils\get_flag_value( $assoc_args, 'post', false );

			// Use excerpts or not.
			$use_excerpts = WP_CLI\Utils\get_flag_value( $assoc_args, 'use-excerpts', true );

			// Do you wish to focus on a particular category?
			$focus = WP_CLI\Utils\get_flag_value( $assoc_args, 'focus', '' );

			// Parse some flags.
			$include_stats = WP_CLI\Utils\get_flag_value( $assoc_args, 'stats', true );
			$do_publish    = WP_CLI\Utils\get_flag_value( $assoc_args, 'publish', false );

			$arguments = array(
				'do_publish'      => $do_publish,
				'include_stats'   => $include_stats,
				'focus'           => $focus,
				'use_excerpts'    => $use_excerpts,
				'post'            => $post,
				'skip_posts'      => $skip_posts,
				'skip_tags'       => $skip_tags,
				'skip_categories' => $skip_categories,
				'days'            => $days,
			);
			$this->dc->create( $arguments );


		}
	}

	// Finally add the command to WP_CLI.
	try {
		WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
	} catch ( Exception $e ) {
		WP_CLI::error( '*** WP_CLI threw an exception: ' . $e->getMessage() );
	}
}
