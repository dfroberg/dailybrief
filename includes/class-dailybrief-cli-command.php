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
		 * Prepare for buffering output to a new post
		 *
		 * @param string $output what to write.
		 * @param bool   $buffer log or stdout.
		 *
		 * @input string @output write to log.
		 */
		private function output( $output, $buffer = false ) {
			if ( false === $buffer ) {
				WP_CLI::log( $output );
			} else {
				$this->content_buffer .= $output;
			}
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
			if ( ! empty( $skip_categories ) ) {
				$exclude_categories = array_merge( explode( ',', $skip_categories ), $this->dc->getAlwaysSkipCategory() );
			}
			// Exclude some tag ids for whatever reason and merge with the always_skip_tags option.
			$skip_tags = WP_CLI\Utils\get_flag_value( $assoc_args, 'skip-tags', '' );
			if ( ! empty( $skip_tags ) ) {
				$exclude_tags = array_merge( explode( ',', $skip_tags ), $this->dc->getAlwaysSkipTags() );
			}
			// Exclude some post_ids for whatever reason.
			$skip_posts = WP_CLI\Utils\get_flag_value( $assoc_args, 'skip-posts', '' );
			if ( ! empty( $skip_posts ) ) {
				$exclude_posts = explode( ',', $skip_posts );
			}

			$status = array( 'publish' );
			$types  = array( 'post' );
			$buffer = false;

			// Parse some flags.
			$post = WP_CLI\Utils\get_flag_value( $assoc_args, 'post', false );
			if ( $post ) {
				// Ok prepare the post.
				$buffer = true;
				WP_CLI::log( '* Preparing post for ' . $today );
			}
			// Use excerpts or not.
			$use_excerpts = WP_CLI\Utils\get_flag_value( $assoc_args, 'use-excerpts', true );
			// Do you wish to focus on a particular category?
			$focus = WP_CLI\Utils\get_flag_value( $assoc_args, 'focus', '' );
			if ( ! empty( $focus ) ) {
				$focus = explode( ',', $focus );
			}
			// Parse some flags.
			$include_stats = WP_CLI\Utils\get_flag_value( $assoc_args, 'stats', true );
			$do_publish    = WP_CLI\Utils\get_flag_value( $assoc_args, 'publish', false );
			// Retrieve posts.
			$page               = 1;
			$article_count      = 0;
			$article_categories = array();
			$article_tags       = array();
			$stats              = '';
			$article            = '';
			$toc_items          = '';
			do {
				$query = new WP_Query(
					array(
						'posts_per_page'   => 30,
						'paged'            => $page,
						'post_status'      => $status,
						'post_type'        => $types,
						'date_query'       => array(
							array(
								'before'    => $before_date,
								'after'     => $after_date,
								'inclusive' => true,
							),
						),
						'tag__not_in'      => $exclude_tags,
						'category__not_in' => $exclude_categories,
						'post__not_in'     => $exclude_posts,
					)
				);

				while ( $query->have_posts() ) {
					$query->the_post();
					$id      = get_the_ID();
					$content = $query->post->post_content;
					$more    = '... <a href="' . get_permalink( $id ) . $this->dc->getUrlSuffix() . '" target="dailybrief">' . $this->dc->getArticleContinue() . '</a>';

					if ( ! $use_excerpts || ! has_excerpt() ) {
						$excerpt = wp_trim_words( wp_strip_all_tags( $content, true ), $this->dc->getExcerptWords(), $more );
					} else {
						$excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $query ), true ), $this->dc->getExcerptWords(), $more );
					}
					$title = $query->post->post_title;
					$date  = $query->post->post_date;
					// Add any attachments on this post to the list of excluded attachments if this post is excluded.
					if ( in_array( $id, $exclude_posts, false ) ) {
						continue;
					}
					// Spit out some posts.
					$article_count ++;

					// Get article categories for stats.
					$c      = get_the_category( $id );
					$c_cats = array();
					if ( $c ) {
						foreach ( $c as $c_cat ) {
							$c_cats[]             = ucwords( $c_cat->category_nicename, "- \t\r\n\f\v" );
							$article_category     = ucwords( $c_cat->category_nicename, "- \t\r\n\f\v" );
							$article_categories[] = $article_category;

						}
					}

					// Get the article tags for stats.
					$t      = get_the_tags( $id );
					$t_tags = array();
					if ( $t ) {
						foreach ( $t as $t_tag ) {
							$t_tags[]       = ucwords( $t_tag->name, "- \t\r\n\f\v" );
							$article_tag    = ucwords( $t_tag->name, "- \t\r\n\f\v" );
							$article_tags[] = $article_tag;
						}
					}
					// Pick a temporary featured image from the posts in the brief to use if featured_image_url is not set.
					if ( '' === $this->dc->getTempFeaturedImageUrl() && '' === $this->dc->getFeaturedImageUrl() ) {
						$this->dc->setTempFeaturedImageUrl( get_the_post_thumbnail_url( $id, 'full' ) );
					}
					// Compile a TOC.
					if ( 1 === $this->dc->getIncludeToc() ) {
						$toc_items .= '<li>';
						if ( 1 === $this->dc->getIncludeTocLocalHrefs() ) {
							$toc_items .= '<a href="#_author_permlink_' . $id . '">';
						}
						$toc_items .= $title . '</a></li>';
					}

					if ( 1 === $this->dc->getIncludeTocLocalHrefs() ) {
						$article .= ( '<a id="_author_permlink_' . $id . '" name="_author_permlink_' . $id . '"></a>' );
					}
					$article .= ( '<img src="' . get_the_post_thumbnail_url( $id, 'full' ) . '">' );
					$article .= ( '<h2><a href="' . get_permalink( $id ) . $this->url_suffix . '" target="dailybrief">' . $title . '</a></h2>' );
					$article .= ( 'Published <strong>' . $date . '</strong> by <strong>' . get_the_author() . '</strong> in <strong>' . implode( ', ', $c_cats ) . '</strong>' );
					$article .= ( '<p>' . $excerpt . '</p>' );
					$article .= ( '<p>Tags: ' . implode( ', ', $t_tags ) . '</p>' );
					$article .= $this->article_delimiter;
					WP_CLI::log( '+ Added: ' . $title );
				}

				// Append to slug with page number
				// Describe time range in title and header / footer macro
				// Generate separate posts if number of articles exceeds posts_per_page.
				$page ++;
			} while ( $query->have_posts() );
			// End of post preparation.
			// Output.
			WP_CLI::log( '--- BEGIN POST ----' );
			// Output Header.
			if ( ! empty( $this->options['header'] ) ) {
				$header = $this->options['header'];

				// Prepare macro subst / stats.
				if ( false !== stripos( $header, '{article_count}' ) ) {
					$header = str_replace( '{article_count}', $article_count, $header );
				} else {
					$stats = $this->article_stats_txt . ' ' . $article_count;
				}

				if ( is_array( $article_categories ) && count( $article_categories ) > 0 ) {
					$article_categories = array_unique( $article_categories );
					if ( false !== stripos( $header, '{article_categories}' ) ) {
						$header = str_replace( '{article_categories}', implode( ', ', $article_categories ), $header );
					} else {
						$stats .= $this->article_stats_cats_txt . ' ' . implode( ', ', $article_categories );
					}
				}

				if ( is_array( $article_tags ) && count( $article_tags ) > 0 ) {
					$article_tags = array_unique( $article_tags );
					if ( false !== stripos( $header, '{article_tags}' ) ) {
						$header = str_replace( '{article_tags}', implode( ', ', $article_tags ), $header );
					} else {
						$stats .= $this->article_stats_tags_txt . ' ' . implode( ', ', $article_tags );
					}
				}

				if ( $include_stats ) {
					$header .= $stats;
				}

				$this->output( $header, $buffer );
			}
			// Output optional TOC.
			if ( 1 === $this->include_toc ) {
				$this->output( '<hr><p><h3>Table of Contents</h3><ul>', $buffer );
				$this->output( $toc_items, $buffer );
				$this->output( '</ul></p><hr>', $buffer );
			}

			// Output article.
			$this->output( $article, $buffer );

			// Output Footer.
			if ( ! empty( $this->options['footer'] ) ) {
				$this->output( $this->options['footer'], $buffer );
			}
			WP_CLI::log( '--- END POST ----' );

			// Create WP Post.
			if ( $post && $article_count > 0 ) {
				// Update the globals to recreate slugs and titles etc if anything changed via args.
				$this->update_globals();

				// Unfurl tags if any.
				$post_tags = array();
				if ( strlen( $this->post_tags ) !== '' ) {
					$post_tags = explode( ',', $this->post_tags );
				}
				// Ok create the post.
				WP_CLI::log( '* Creating post with ' . $article_count . ' articles.' );
				// Do some sanity checks.
				// Call create_post here.
				$wp_insert_post_result = $this->create_post();
				if ( $wp_insert_post_result > 0 ) {
					$this->post_id_created = $wp_insert_post_result;
					WP_CLI::log( '* Created ' . $this->post_id_created . ' - "' . $this->post_title . '" on ' . $this->slug );
					// Append Tags if any set.
					if ( is_array( $post_tags ) && count( $post_tags ) > 0 ) {
						$set_tags = wp_set_post_tags( $this->post_id_created, $post_tags, false );
						if ( ! is_wp_error( $set_tags ) ) {
							WP_CLI::log( '* Set tags ' . implode( ', ', $post_tags ) );
						} else {
							WP_CLI::error( "*** Error - could not set the tags...\n" . $set_tags->get_error_message() );
						}
					} else {
						WP_CLI::warning( '! No tags to set. (This will cause issues if you have no default tags in SteemPress set) ' . implode( ', ', $post_tags ) );
					}
					// Force the use of a --publish flag.
					if ( $do_publish ) {
						// Transition post to publish state.
						wp_publish_post( $this->post_id_created );
						WP_CLI::log( '* Post is now Published ' );

						if ( ! class_exists( 'Steempress_sp_Admin' ) ) {
							WP_CLI::warning( '? SteemPress NOT available (did you install it?), can not post to steem. ' );
						} else {
							WP_CLI::log( '* SteemPress IS available, can post to steem, so trying that now ' );

							// Since we're using another plugin directly we'll try and catch whatever goes wrong.
							try {
								$test = new Steempress_sp_Admin( 'steempress_sp', '2.3' );
								$test->Steempress_sp_publish( $this->post_id_created );
								// Alt Steempress_sp_Admin::Steempress_sp_publish($this->post_id_created);.
								$steempress_sp_permlink = get_post_meta( $this->post_id_created, 'steempress_sp_permlink' );
								$steempress_sp_author   = get_post_meta( $this->post_id_created, 'steempress_sp_author' );
								if ( ! empty( $steempress_sp_permlink ) && ! empty( $steempress_sp_author ) ) {
									WP_CLI::log( '* Posted to SteemPress API with: ' . $steempress_sp_author . ' / ' . $steempress_sp_permlink );
								} else {
									WP_CLI::warning( '? SteemPress API post failed for some reason :-( ' );
								}
							} catch ( Exception $e ) {
								WP_CLI::error( '*** Error - SteemPress Call Blew up ' . $e->getMessage() );
							}
						}
					}
				} else {
					WP_CLI::error( '*** Error - could not create the post...\n' . $wp_insert_post_result->get_error_message() );
				}
			}
		}
	}

	// Finally add the command to WP_CLI.
	try {
		WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
	} catch ( Exception $e ) {
		WP_CLI::error( '*** WP_CLI threw an exception: ' . $e->getMessage() );
	}
}
