<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.froberg.org
 * @since      1.0.0
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dailybrief
 * @subpackage Dailybrief/includes
 * @author     Daniel Froberg <danny@froberg.org>
 */
class Dailybrief {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Dailybrief_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = 'dailybrief';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Placeholder for the date suffix appended to the slug of the generated post
	 *
	 * @var string
	 */
	private $date_suffix = '';
	/**
	 * Placeholder for the image url we got to use as featured image.
	 *
	 * @var string
	 */
	private $temp_featured_image_url = '';
	/**
	 * This contains the created WP Post ID if successfully generated.
	 *
	 * @var int
	 */
	private $post_id_created = 0;
	/**
	 * Options array from WP DB.
	 *
	 * @var array
	 */
	private $options;
	/**
	 * Shall we debug?
	 *
	 * @var integer
	 */
	private $debug;
	/**
	 * Shall we include the Table of Contents in the brief?
	 *
	 * @var integer
	 */
	private $include_toc;
	/**
	 * Due to a bug in Steemit Condenser and many other condensers, do we want to make local anchor HREFs to the articles?
	 *
	 * @var integer
	 */
	private $include_toc_local_hrefs;
	/**
	 * Allow for analytics suffixes to be appended to the outbound URLs.
	 *
	 * @var integer
	 */
	private $url_suffix;
	/**
	 * How many words do we want in the article excerpts of the brief?
	 *
	 * @var int
	 */
	private $excerpt_words;
	/**
	 * The Title of the WP & Steem Post we generate (Will be suffixed with the date).
	 *
	 * @var integer
	 */
	private $post_title;
	/**
	 * Which author do we want to post as in WP?
	 *
	 * @var string
	 */
	private $author_id;
	/**
	 * Under what category do we post to in WP?
	 *
	 * @var integer
	 */
	private $post_category;
	/**
	 * What WP tags do we include in our WP post, these will be sent to steem if there is room.
	 *
	 * @var integer
	 */
	private $post_tags;
	/**
	 * Always skip these categories, this is among other things used to not include the briefs category in the brief we generate.
	 *
	 * @var string
	 */
	private $always_skip_category;
	/**
	 * Skip these tags.
	 *
	 * @var integer
	 */
	private $always_skip_tags;
	/**
	 * Base slug of the WP & Steem post (will be suffixed with the date)
	 *
	 * @var string
	 */
	private $slug;
	/**
	 * Will WP commenting on the brief be open?
	 *
	 * @var string
	 */
	private $comment_status;
	/**
	 * Will WP Pings be allowed?
	 *
	 * @var string
	 */
	private $ping_status;
	/**
	 * What status should the initial WP post have?
	 *
	 * @var string
	 */
	private $post_status;
	/**
	 * Only 'post' is allowed atm.
	 *
	 * @var string
	 */
	private $post_type;
	/**
	 * What should we put inbetween the articles in the brief. i.e. <hr>.
	 *
	 * @var string
	 */
	private $article_delimiter;
	/**
	 * What text should we have to signify that the  reader can click to continue reading?
	 *
	 * @var string
	 */
	private $article_continue;
	/**
	 * Article statistics text.
	 *
	 * @var string
	 */
	private $article_stats_txt;
	/**
	 * Article categories statistics text.
	 *
	 * @var string
	 */
	private $article_stats_cats_txt;
	/**
	 * Article tags statistics text.
	 *
	 * @var string
	 */
	private $article_stats_tags_txt;
	/**
	 * Preset or collected featured image URL.
	 *
	 * @var string
	 */
	private $featured_image_url;
	/**
	 * Contains what should be sent to the post.
	 *
	 * @var string
	 */
	private $content_buffer = '';
	/**
	 * Use excerpts.
	 *
	 * @var string
	 */
	private $use_excerpts = '0';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_use_excerpts() {
		return $this->use_excerpts;
	}

	/**
	 * Set Use excerpts.
	 *
	 * @param string $use_excerpts Set Use excerpts.
	 */
	public function set_use_excerpts( $use_excerpts ) {
		$this->use_excerpts = $use_excerpts;
	}

	/**
	 * Focus category.
	 *
	 * @var string
	 */
	private $focus = '-1';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_focus() {
		return $this->focus;
	}

	/**
	 * Set focus.
	 *
	 * @param string $focus Set focus category.
	 */
	public function set_focus( $focus ) {
		if ( ! is_array( $focus ) ) {
			$this->focus = $focus;
		} else {
			$this->focus = implode( ',', $focus );
		}
	}

	/**
	 * Cron Publish Enabled category.
	 *
	 * @var string
	 */
	private $cron_publish = '0';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_cron_publish() {
		return $this->cron_publish;
	}

	/**
	 * Set cron_publish.
	 *
	 * @param string $cron_publish Set cron_publish enabled or not.
	 */
	public function set_cron_publish( $cron_publish ) {
		$this->cron_publish = $cron_publish;
	}

	/**
	 * Cron Publish Enabled category.
	 *
	 * @var string
	 */
	private $cron_pause = '0';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_cron_pause() {
		return $this->cron_pause;
	}

	/**
	 * Set cron_pause.
	 *
	 * @param string $cron_pause Set cron pause enabled or not.
	 */
	public function set_cron_pause( $cron_pause ) {
		$this->cron_pause = $cron_pause;
	}
	/**
	 * Period.
	 *
	 * @var string
	 */
	private $period = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_period() {
		return $this->footer;
	}

	/**
	 * Set period.
	 *
	 * @param string $period Set period.
	 */
	public function set_period( $period ) {
		$this->period = $period;
	}
	/**
	 * Start Date.
	 *
	 * @var string
	 */
	private $start_date = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * Set start_date.
	 *
	 * @param string $start_date Set start_date.
	 */
	public function set_start_date( $start_date ) {
		$this->start_date = $start_date;
	}
	/**
	 * End Date.
	 *
	 * @var string
	 */
	private $end_date = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * Set end_date.
	 *
	 * @param string $end_date Set end_date.
	 */
	public function set_end_date( $end_date ) {
		$this->end_date = $end_date;
	}
	/**
	 * Footer.
	 *
	 * @var string
	 */
	private $footer = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_footer() {
		return $this->footer;
	}

	/**
	 * Set footer.
	 *
	 * @param string $footer Set  footer.
	 */
	public function set_footer( $footer ) {
		$this->footer = $footer;
	}
	/**
	 * Header.
	 *
	 * @var string
	 */
	private $header = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_header() {
		return $this->header;
	}

	/**
	 * Set header.
	 *
	 * @param string $header Set  header.
	 */
	public function set_header( $header ) {
		$this->header = $header;
	}
	/**
	 * Table of Contents header.
	 *
	 * @var string
	 */
	private $toc_header = '';

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_toc_header() {
		return $this->toc_header;
	}

	/**
	 * Set table of contents header.
	 *
	 * @param string $toc_header Set table of contents header.
	 */
	public function set_toc_header( $toc_header ) {
		$this->toc_header = $toc_header;
	}
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'DAILYBRIEF_VERSION' ) ) {
			$this->version = DAILYBRIEF_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'dailybrief';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->update_globals();
		add_action( self::CRON_HOOK, array( $this, 'dailybrief_do_daily_event' ) );

	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_date_suffix() {
		return $this->date_suffix;
	}

	/**
	 * Setter.
	 *
	 * @param string $date_suffix Set suffix.
	 */
	public function set_date_suffix( $date_suffix ) {
		$this->date_suffix = $date_suffix;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_temp_featured_image_url() {
		return $this->temp_featured_image_url;
	}

	/**
	 * Setter.
	 *
	 * @param string $temp_featured_image_url Placeholder for temp URL.
	 */
	public function set_temp_featured_image_url( $temp_featured_image_url ) {
		$this->temp_featured_image_url = $temp_featured_image_url;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_post_id_created() {
		return $this->post_id_created;
	}

	/**
	 * Setter.
	 *
	 * @param int $post_id_created WP Post ID created.
	 */
	public function set_post_id_created( $post_id_created ) {
		$this->post_id_created = $post_id_created;
	}

	/**
	 * Getter.
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Setter.
	 *
	 * @param array $options Options array.
	 */
	public function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_debug() {
		return $this->debug;
	}

	/**
	 * Setter.
	 *
	 * @param int $debug Shall we debug.
	 */
	public function set_debug( $debug ) {
		$this->debug = $debug;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_include_toc() {
		return $this->include_toc;
	}

	/**
	 * Setter.
	 *
	 * @param int $include_toc Table of contents.
	 */
	public function set_include_toc( $include_toc ) {
		$this->include_toc = $include_toc;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_include_toc_local_hrefs() {
		return $this->include_toc_local_hrefs;
	}

	/**
	 * Setter.
	 *
	 * @param int $include_toc_local_hrefs Ahrefs in TOC.
	 */
	public function set_include_toc_local_hrefs( $include_toc_local_hrefs ) {
		$this->include_toc_local_hrefs = $include_toc_local_hrefs;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_url_suffix() {
		return $this->url_suffix;
	}

	/**
	 * Setter.
	 *
	 * @param string $url_suffix Add stuff to outbound URLS.
	 */
	public function set_url_suffix( $url_suffix ) {
		$this->url_suffix = $url_suffix;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_excerpt_words() {
		return $this->excerpt_words;
	}

	/**
	 * Setter.
	 *
	 * @param int $excerpt_words How many words to use.
	 */
	public function set_excerpt_words( $excerpt_words ) {
		$this->excerpt_words = $excerpt_words;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_post_title() {
		return $this->post_title;
	}

	/**
	 * Setter.
	 *
	 * @param int $post_title WP Post Title.
	 */
	public function set_post_title( $post_title ) {
		$this->post_title = $post_title;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_author_id() {
		return $this->author_id;
	}

	/**
	 * Setter.
	 *
	 * @param string $author_id Author ID.
	 */
	public function set_author_id( $author_id ) {
		$this->author_id = $author_id;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_post_category() {
		return $this->post_category;
	}

	/**
	 * Setter.
	 *
	 * @param int $post_category WP Post category.
	 */
	public function set_post_category( $post_category ) {
		$this->post_category = $post_category;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_post_tags() {
		return $this->post_tags;
	}

	/**
	 * Setter.
	 *
	 * @param int $post_tags What Wp Post tags to set.
	 */
	public function set_post_tags( $post_tags ) {
		$this->post_tags = $post_tags;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_always_skip_category() {
		return $this->always_skip_category;
	}

	/**
	 * Setter.
	 *
	 * @param string $always_skip_category Don't include posts from these categories.
	 */
	public function set_always_skip_category( $always_skip_category ) {
		$this->always_skip_category = $always_skip_category;
	}

	/**
	 * Getter.
	 *
	 * @return int
	 */
	public function get_always_skip_tags() {
		return $this->always_skip_tags;
	}

	/**
	 * Setter.
	 *
	 * @param int $always_skip_tags Don't include posts from these tags.
	 */
	public function set_always_skip_tags( $always_skip_tags ) {
		$this->always_skip_tags = $always_skip_tags;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Setter.
	 *
	 * @param string $slug Base slug to use.
	 */
	public function set_slug( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_comment_status() {
		return $this->comment_status;
	}

	/**
	 * Setter.
	 *
	 * @param string $comment_status Open or closed for comments.
	 */
	public function set_comment_status( $comment_status ) {
		$this->comment_status = $comment_status;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_ping_status() {
		return $this->ping_status;
	}

	/**
	 * Setter.
	 *
	 * @param string $ping_status Open or Closed for pings.
	 */
	public function set_ping_status( $ping_status ) {
		$this->ping_status = $ping_status;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_post_status() {
		return $this->post_status;
	}

	/**
	 * Setter.
	 *
	 * @param string $post_status Draft or Publish.
	 */
	public function set_post_status( $post_status ) {
		$this->post_status = $post_status;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Setter.
	 *
	 * @param string $post_type Always post for now.
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_article_delimiter() {
		return $this->article_delimiter;
	}

	/**
	 * Setter.
	 *
	 * @param string $article_delimiter Stuff between articles in briefs.
	 */
	public function set_article_delimiter( $article_delimiter ) {
		$this->article_delimiter = $article_delimiter;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_article_continue() {
		return $this->article_continue;
	}

	/**
	 * Setter.
	 *
	 * @param string $article_continue Read more.
	 */
	public function set_article_continue( $article_continue ) {
		$this->article_continue = $article_continue;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_article_stats_txt() {
		return $this->article_stats_txt;
	}

	/**
	 * Setter.
	 *
	 * @param string $article_stats_txt Intro to article stats.
	 */
	public function set_article_stats_txt( $article_stats_txt ) {
		$this->article_stats_txt = $article_stats_txt;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_article_stats_cats_txt() {
		return $this->article_stats_cats_txt;
	}

	/**
	 * Setter.
	 *
	 * @param string $article_stats_cats_txt Intro to category stats.
	 */
	public function set_article_stats_cats_txt( $article_stats_cats_txt ) {
		$this->article_stats_cats_txt = $article_stats_cats_txt;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_article_stats_tags_txt() {
		return $this->article_stats_tags_txt;
	}

	/**
	 * Setter.
	 *
	 * @param string $article_stats_tags_txt Intro to tags stats.
	 */
	public function set_article_stats_tags_txt( $article_stats_tags_txt ) {
		$this->article_stats_tags_txt = $article_stats_tags_txt;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_featured_image_url() {
		return $this->featured_image_url;
	}

	/**
	 * Setter.
	 *
	 * @param string $featured_image_url Featured Image URL to use.
	 */
	public function set_featured_image_url( $featured_image_url ) {
		$this->featured_image_url = $featured_image_url;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_content_buffer() {
		return $this->content_buffer;
	}

	/**
	 * Setter.
	 *
	 * @param string $content_buffer Buffer to compile WP Post content in.
	 */
	public function set_content_buffer( $content_buffer ) {
		$this->content_buffer = $content_buffer;
	}

	/**
	 * Cron Hook name
	 */
	const CRON_HOOK = 'dailybrief_daily_event';

	/**
	 * Do daily CRON job.
	 *
	 * @param bool $manual Enable override of pause.
	 *
	 * @return array
	 */
	function dailybrief_do_daily_event( $manual = false ) {
		// do brief every day!
		$dc = new Dailybrief();
		$dc->update_globals();
		$options = $dc->get_options();
		// Dead end if CRON is paused.
		if ( true === $manual || '0' === $options['cron_pause'] ) {
			// Generate post.
			$dailybrief = $dc->create(
				array(
					'preview'         => false,
					'period'          => $options['period'],
					'days'            => date( 'Y-m-d', strtotime( $options['start_date'] ) ),
					'start'           => date( 'Y-m-d H:i:s', strtotime( $options['start_date'] ) ),
					'end'             => date( 'Y-m-d H:i:s', strtotime( $options['end_date'] ) ),
					'use-excerpts'    => $options['use_excerpts'],
					'skip-categories' => $options['skip_categories'],
					'post'            => true,
					'publish'         => $options['cron_publish'],
				)
			);
			return $dailybrief;
		} else {
			return array( 'error' => 'CRON Publishing is paused.' );
		}
	}

	/**
	 * Setup the cron jobs required for this plugin.
	 */
	public static function activator() {
		// Use wp_next_scheduled to check if the event is already scheduled.
		$timestamp = wp_next_scheduled( self::CRON_HOOK );

		// If $timestamp == false schedule daily backups since it hasn't been done previously.
		if ( false == $timestamp ) {
			// Lets schedule the next brief for tomorrow after midnight according to this sites Timezone.
			try {
				$date = new DateTime( 'tomorrow', WpDateTimeZone::getWpTimezone() );
			} catch ( Exception $e ) {
				wp_die( $e->getMessage(), 'DailyBrief Exploded' );
			}
			$timestamp = $date->getTimestamp();

			// Schedule the event for right now, then to repeat daily using the hook 'dailybrief_daily_event'.
			wp_schedule_event( $timestamp, 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Remove any cron jobs for this plugin.
	 */
	public static function deactivator() {
		// Get the timestamp for the next event.
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		wp_unschedule_event( $timestamp, self::CRON_HOOK );
	}

	/**
	 * Re applies the defaults with options
	 */
	public function update_globals() {
		$this->options                 = get_option( $this->plugin_name /* 'dailybrief_options' */, array() );
		$this->debug                   = $this->get_option_default( 'debug', '0' ); // 1 for on
		$this->include_toc             = $this->get_option_default( 'include_toc', '1' ); // 1 for on / 0 for off
		$this->include_toc_local_hrefs = $this->get_option_default( 'include_toc_local_hrefs', '1' ); // 1 for on / 0 for off
		$this->toc_header              = $this->get_option_default( 'toc_header', 'Table of Contents' );
		$this->url_suffix              = $this->get_option_default( 'url_suffix', '?utm_campaign=steempress&utm=dailybrief' ); // set ''.
		$this->excerpt_words           = $this->get_option_default( 'excerpt_words', '100' );
		$this->post_title              = $this->get_option_default( 'post_title', 'The Daily Brief' ) . ' ' . $this->date_suffix;
		$this->author_id               = $this->get_option_default( 'author_id', '1' );
		$this->post_category           = $this->get_option_default( 'post_category', '1' ); // 1,2,8
		$this->post_tags               = $this->get_option_default( 'post_tags', '' ); // life,blog,news.
		$this->always_skip_category    = $this->get_option_default( 'always_skip_category', -$this->post_category ); // Always skip the category of Daily Brief Posts.
		$this->always_skip_tags        = $this->get_option_default( 'always_skip_tag', '0' );
		$this->slug                    = $this->get_option_default( 'slug', 'the-daily-brief' ) . '-' . $this->date_suffix;
		$this->comment_status          = $this->get_option_default( 'comment_status', 'open' );
		$this->ping_status             = $this->get_option_default( 'ping_status', 'closed' );
		$this->post_status             = $this->get_option_default( 'post_status', 'publish' );
		$this->post_type               = $this->get_option_default( 'post_type', 'post' );
		$this->article_delimiter       = $this->get_option_default( 'article_delimiter', '<hr>' );
		$this->article_continue        = $this->get_option_default( 'article_continue', 'Continue&nbsp;-&gt;' );
		$this->article_stats_txt       = $this->get_option_default( 'article_stats_txt', '<hr>Articles in this brief: ' );
		$this->article_stats_cats_txt  = $this->get_option_default( 'article_stats_cats_txt', '<br>Categories in this brief: ' );
		$this->article_stats_tags_txt  = $this->get_option_default( 'article_stats_tags_txt', '<br>Tags in this brief: ' );
		$this->featured_image_url      = $this->get_option_default( 'featured_image_url', '' );
		$this->header                  = $this->get_option_default( 'header', '<p>This daily summary contains <strong>{article_count}</strong> articles about; <em>{article_tags}</em> in the following categories; <em>{article_categories}</em>.</p>' );
		$this->footer                  = $this->get_option_default( 'footer', '<center><h2>Thank you for following our coverage.</h2></center>' );
		$this->period                  = $this->get_option_default( 'period', 'day' );
		$this->start_date              = $this->get_option_default( 'start_date', '-1 day' );
		$this->end_date                = $this->get_option_default( 'end_date', '-1 day' );
		$this->focus                   = $this->get_option_default( 'focus', '-1' );
		$this->cron_publish            = $this->get_option_default( 'cron_publish', '1' );
		$this->cron_pause              = $this->get_option_default( 'cron_pause', '0' );
		$this->use_excerpts            = $this->get_option_default( 'use_excerpts', '0' );
		$this->skip_categories         = $this->get_option_default( 'skip_categories', '-1' );

	}

	/**
	 * Return an dailybrief_options value if exists otherwise return the default.
	 *
	 * @param string $option  option name.
	 * @param mixed  $default default value to return.
	 *
	 * @return mixed
	 */
	private function get_option_default( $option, $default ) {
		// Should only run once per option not found.
		if ( ! isset( $this->options[ $option ] ) ) {
			$this->set_option( $option, $default );
			return $default;
		}

		return $this->options[ $option ];
	}

	/**
	 * Creates a new WordPress post with all the specified arguments set by options
	 *
	 * @return mixed post_id
	 */
	public function create_post() {
		if ( _mb_strlen( $this->content_buffer ) > 65280 ) {
			return new WP_Error( 'error', 'Make sure your text is smaller than 65280 characters.' );
		}
		$post_category = explode( ',', $this->post_category );
		if ( empty( $post_category ) ) {
			$post_category[] = 1; // "Uncategorized".
		}
		$post_id = wp_insert_post(
			array(
				'comment_status' => $this->comment_status,
				'ping_status'    => $this->ping_status,
				'post_author'    => $this->author_id,
				'post_name'      => $this->slug,
				'post_title'     => $this->post_title,
				'post_content'   => $this->content_buffer,
				'post_status'    => 'draft',
				'post_type'      => $this->post_type,
				'post_category'  => $post_category,
			)
		);
		if ( '' !== $this->featured_image_url ) {
			$dailybrief_featured_image_id = attachment_url_to_postid( $this->featured_image_url );
		} else {
			$dailybrief_featured_image_id = attachment_url_to_postid( $this->temp_featured_image_url );
		}
		if ( 0 === $dailybrief_featured_image_id ) {
			$this->wpcliwarn( 'Unable to set featured image, make sure you have uploaded the image you want to use to your sites media library and set the featured_image_url option with its complete URL.' );

			return $post_id;
		}
		// Set Featured image if available.
		set_post_thumbnail( $post_id, $dailybrief_featured_image_id );

		return $post_id;
	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Dailybrief_Loader. Orchestrates the hooks of the plugin.
	 * - Dailybrief_i18n. Defines internationalization functionality.
	 * - Dailybrief_Admin. Defines all hooks for the admin area.
	 * - Dailybrief_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-dailybrief-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . '../includes/class-dailybrief-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __FILE__ ) . '../admin/class-dailybrief-admin.php';
		require_once plugin_dir_path( __FILE__ ) . '../admin/class-submenu.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __FILE__ ) . '../public/class-dailybrief-public.php';

		$this->loader = new Dailybrief_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dailybrief_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Dailybrief_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Dailybrief_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		// Add Settings link to the plugin.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Dailybrief_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Dailybrief_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Set option parameter
	 *
	 * @param string $option_name  Name of option to set.
	 * @param string $option_value Value of option to set.
	 *
	 * @return bool
	 */
	public function set_option( $option_name, $option_value ) {

		if ( ! empty( $this->options ) ) {
			$this->options[ $option_name ] = $option_value;

			// The option already exists, so we just update it.
			return update_option( $this->plugin_name, $this->options );
		} else {
			// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			$deprecated                    = null;
			$autoload                      = 'no';
			$this->options[ $option_name ] = $option_value;

			return add_option( $this->plugin_name, $this->options, $deprecated, $autoload );
		}
	}

	/**
	 * Return value of specified option.
	 *
	 * @param string $option_name Name of option to get value for.
	 *
	 * @return mixed
	 */
	public function get_option( $option_name ) {
		return $this->options[ $option_name ];
	}

	/**
	 * Logging via WP_CLI
	 *
	 * @param string $message Logging message.
	 */
	public function wpclilog( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $message );
		}
	}

	/**
	 * Logging via WP_CLI
	 *
	 * @param string $message Logging message.
	 */
	public function wpcliwarn( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::warning( $message );
		}
	}

	/**
	 * Logging via WP_CLI
	 *
	 * @param string $message Logging message.
	 *
	 * @throws \WP_CLI\ExitException Stops processing on Error.
	 */
	public function wpclierror( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( $message );
		}
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
			$this->wpclilog( $output );
		} else {
			$this->content_buffer .= $output;
		}
	}

	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return  bool
	 */
	function is_https() {
		if ( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) !== 'off' ) {
			return true;
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https' ) {
			return true;
		} elseif ( ! empty( $_SERVER['HTTP_FRONT_END_HTTPS'] ) && strtolower( $_SERVER['HTTP_FRONT_END_HTTPS'] ) !== 'off' ) {
			return true;
		}

		return false;
	}

	/**
	 * Parse argument array
	 *
	 * @param array  $arguments Array of arguments sent to function.
	 * @param string $parameter Name of parameter to return.
	 * @param mixed  $default   Default value of the parameter.
	 *
	 * @return mixed
	 */
	private function parse_arguments( $arguments, $parameter, $default ) {
		if ( ! empty( $arguments[ $parameter ] ) ) {
			return $arguments[ $parameter ];
		}

		return $default;
	}

	/**
	 * Create the WP Post.
	 *
	 * @param array $arguments Array of arguments sent to function.
	 *
	 * @return array
	 * @throws \WP_CLI\ExitException Stops processing on Error.
	 */
	public function create( $arguments ) {
		global
		$wpdb;
		$status  = array( 'publish' );
		$types   = array( 'post' );
		$buffer  = false;
		$preview = $this->parse_arguments( $arguments, 'preview', false );
		if ( $preview ) {
			$buffer = true;
		}
		/**
		 * Period to run brief against.
		 * Can be day, for a specific day in the past or today, or days to specify range of days (longer than one day).
		 */
		$period = $this->parse_arguments( $arguments, 'period', 'day' );

		if ( 'day' === $period || empty( $period ) ) {
			$days         = $this->parse_arguments( $arguments, 'days', '-1 day' );
			$today        = strtotime( $days );
			$begin_period = strtotime( date( 'Y-m-d 00:00:00', $today ) );
			$end_period   = strtotime( date( 'Y-m-d 23:59:59', $today ) );
			$before_date  = date( 'Y-m-d H:i:s', $end_period );
			$after_date   = date( 'Y-m-d H:i:s', $begin_period );
		} elseif ( 'range' === $period ) {
			$startday     = $this->parse_arguments( $arguments, 'start', '-1 day 00:00:00' );
			$endday       = $this->parse_arguments( $arguments, 'end', '-1 day 23:59:59' );
			$begin_period = strtotime( $startday );
			$end_period   = strtotime( $endday );
			$before_date  = date( 'Y-m-d H:i:s', $end_period );
			$after_date   = date( 'Y-m-d H:i:s', $begin_period );
		}
		if ( substr( $after_date, 0, 10 ) === substr( $before_date, 0, 10 ) ) {
			$today_suffix = '' . substr( $after_date, 0, 10 );
		} else {
			$today_suffix = '' . substr( $after_date, 0, 10 ) . '--' . substr( $before_date, 0, 10 );
		}

		$this->set_date_suffix( $today_suffix ); // used for post-title & slug suffix, contains the date it relates to.

		// Exclude some category ids for whatever reason and merge with the always_skip_category option.
		$skip_categories = $this->parse_arguments( $arguments, 'skip-categories', '-1' );
		if ( ! empty( $skip_categories ) ) {
			$exclude_categories = explode( ',', $skip_categories . ',' . $this->get_always_skip_category() );
		} else {
			$exclude_categories = explode( ',', $this->get_always_skip_category() );
		}
		// Exclude some tag ids for whatever reason and merge with the always_skip_tags option.
		$skip_tags = $this->parse_arguments( $arguments, 'skip-tags', '' );
		if ( ! empty( $skip_tags ) ) {
			$exclude_tags = array_merge( explode( ',', $skip_tags ), $this->get_always_skip_tags() );
		} else {
			$exclude_tags = array();
		}
		// Exclude some post_ids for whatever reason.
		$skip_posts = $this->parse_arguments( $arguments, 'skip-posts', '' );
		if ( ! empty( $skip_posts ) ) {
			$exclude_posts = explode( ',', $skip_posts );
		} else {
			$exclude_posts = array();
		}

		// Parse some flags.
		$post = $this->parse_arguments( $arguments, 'post', false );
		if ( $post ) {
			// Ok prepare the post.
			$buffer = true;
			$this->wpclilog( '* Preparing post for ' . $today_suffix );
		}

		// Use excerpts or not.
		$use_excerpts = $this->parse_arguments( $arguments, 'use-excerpts', '0' );
		// Do you wish to focus on a particular category?
		$focus = $this->parse_arguments( $arguments, 'focus', '' );
		if ( ! empty( $focus ) ) {
			if ( ! is_array( $focus ) ) {
				$focus = explode( ',', $focus );
			}
			// Already an array.
		} else {
			$focus = explode( ',', $this->get_focus() );
		}
		// Parse some flags.
		$include_stats = $this->parse_arguments( $arguments, 'stats', true );
		$do_publish    = $this->parse_arguments( $arguments, 'publish', false );
		// Retrieve posts.
		$page               = 1;
		$article_count      = 0;
		$article_categories = array();
		$article_tags       = array();
		$stats              = '';
		$article            = '';
		$toc_items          = '';
		$schema             = ( is_ssl() ? 'https' : 'http' );

		if ( 'range' === $period ) {
			$date_query_start = array(
				'year'    => date( 'Y', $begin_period ),
				'month'   => date( 'm', $begin_period ),
				'day'     => date( 'd', $begin_period ),
				'hour'    => date( 'H', $begin_period ),
				'minute'  => date( 'i', $begin_period ),
				'second'  => date( 's', $begin_period ),
				'compare' => '>=',
				'column'  => 'post_date',
			);
			$date_query_end   = array(
				'year'    => date( 'Y', $end_period ),
				'month'   => date( 'm', $end_period ),
				'day'     => date( 'd', $end_period ),
				'hour'    => date( 'H', $end_period ),
				'minute'  => date( 'i', $end_period ),
				'second'  => date( 's', $end_period ),
				'compare' => '<=',
				'column'  => 'post_date',
			);
		} elseif ( 'day' === $period ) {
			$date_query_start = array(
				'year'    => date( 'Y', $begin_period ),
				'month'   => date( 'm', $begin_period ),
				'day'     => date( 'd', $begin_period ),
				'hour'    => '00',
				'minute'  => '00',
				'second'  => '00',
				'compare' => '>=',
				'column'  => 'post_date',
			);
			$date_query_end   = array(
				'year'    => date( 'Y', $end_period ),
				'month'   => date( 'm', $end_period ),
				'day'     => date( 'd', $end_period ),
				'hour'    => '23',
				'minute'  => '59',
				'second'  => '59',
				'compare' => '<=',
				'column'  => 'post_date',
			);
		}
		// https://generatewp.com/wp_date_query/ .
		$date_query = array(
			'relation' => 'AND',
			$date_query_start,
			$date_query_end,
		);
		// Sanity check categories selected.
		if ( ! is_array( $exclude_categories ) && '' !== $exclude_categories ) {
			$exclude_categories = explode( ',', $exclude_categories );
		}
		$cats = array_merge( $exclude_categories, array( -$this->get_post_category() ), $focus );

		do {
			$query_array = array(
				'posts_per_page' => 30,
				'paged'          => $page,
				'post_status'    => $status,
				'post_type'      => $types,
				'date_query'     => $date_query,
				'cat'            => $cats,
				'tag__not_in'    => $exclude_tags,
				'post__not_in'   => $exclude_posts,
			);

			$query = new WP_Query( $query_array );

			if ( '' !== $wpdb->last_error ) :

				$str   = htmlspecialchars( print_r( $wpdb->last_result, true ), ENT_QUOTES );
				$query = htmlspecialchars( print_r( $wpdb->last_query, true ), ENT_QUOTES );

				return array(
					'error'      => '<div id="error"><p class="wpdberror"><strong>WordPress database error:</strong> [' . $str . ']<br /><code>' . $query . '</code></p></div>',
					'post_title' => 'Error',
					'content'    => 'Error',
				);

			endif;
			while ( $query->have_posts() ) {
				$query->the_post();
				$id      = get_the_ID();
				$content = $query->post->post_content;
				$more    = '... <a href="' . get_permalink( $id ) . $this->get_url_suffix() . '" target="dailybrief">' . $this->get_article_continue() . '</a>';

				if ( false === $use_excerpts || '0' === $use_excerpts || ! has_excerpt() ) {
					$excerpt = wp_trim_words( wp_strip_all_tags( $content, true ), $this->get_excerpt_words(), $more );
				} else {
					$excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $query ), true ), $this->get_excerpt_words(), $more );
				}
				$title = $query->post->post_title;
				$date  = $query->post->post_date;
				// Skip any post that is excluded.
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
				if ( '' === $this->get_temp_featured_image_url() && '' === $this->get_featured_image_url() ) {
					$this->set_temp_featured_image_url( get_the_post_thumbnail_url( $id, 'full' ) );
				}
				// Compile a TOC.
				if ( '1' === $this->get_include_toc() ) {
					$toc_items .= '<li>';
					if ( '1' === $this->get_include_toc_local_hrefs() ) {
						$toc_items .= '<a href="#_author_permlink_' . $id . '">';
					}
					$toc_items .= $title . '</a></li>';
				}

				if ( '1' === $this->get_include_toc_local_hrefs() ) {
					$article .= ( '<a id="_author_permlink_' . $id . '" name="_author_permlink_' . $id . '"></a>' );
				}
				if ( has_post_thumbnail( $id ) ) {
					$post_thumbnail = get_the_post_thumbnail_url( $id, 'full' );
					if ( false === stripos( $post_thumbnail, $schema ) ) {
						$post_thumbnail = get_site_url( null, '', $schema ) . $post_thumbnail;
					}
					// Lets recheck that we got something.
					if ( false !== stripos( $post_thumbnail, $schema ) ) {
						$article .= ( '<img src="' . $post_thumbnail . '">' );
					}
				}
				$article .= ( '<h2><a href="' . get_permalink( $id ) . $this->url_suffix . '" target="dailybrief">' . $title . '</a></h2>' );
				$article .= ( 'Published <strong>' . $date . '</strong> by <strong>' . ( get_the_author() ?: 'Guest Author' ) . '</strong> in <strong>' . implode( ', ', $c_cats ) . '</strong>' );
				$article .= ( '<p>' . $excerpt . '</p>' );
				$article .= ( '<p>Tags: ' . implode( ', ', $t_tags ) . '</p>' );
				$article .= $this->article_delimiter;
				$this->wpclilog( '+ Added: ' . $title );
			}

			// Append to slug with page number
			// Describe time range in title and header / footer macro
			// Generate separate posts if number of articles exceeds posts_per_page.
			$page ++;
		} while ( $query->have_posts() );
		// End of post preparation.
		// Output.
		$this->wpclilog( '--- BEGIN POST ----' );
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
		if ( $article_count > 0 ) {
			if ( '1' === $this->get_include_toc() ) {
				$this->output( '<hr><p><h3>', $buffer );
				$this->output( $this->get_toc_header(), $buffer );
				$this->output( '</h3><ul>', $buffer );
				$this->output( $toc_items, $buffer );
				$this->output( '</ul></p><hr>', $buffer );
			}
		} else {
			$this->output( '<center><h3>Currently No articles available to Brief about.<br>Check your settings.</h3>Do you have any posts during the specified period or do you have any Focus category set that interferes?</center>', $buffer );
		}

		// Output article.
		$this->output( $article, $buffer );

		// Output Footer.
		if ( ! empty( $this->options['footer'] ) ) {
			// Prepare macro subst / stats.
			$footer = $this->options['footer'];

			if ( false !== stripos( $footer, '{article_count}' ) ) {
				$footer = str_replace( '{article_count}', $article_count, $footer );
			}
			if ( is_array( $article_categories ) && count( $article_categories ) > 0 ) {
				$article_categories = array_unique( $article_categories );
				if ( false !== stripos( $footer, '{article_categories}' ) ) {
					$footer = str_replace( '{article_categories}', implode( ', ', $article_categories ), $footer );
				}
			}

			if ( is_array( $article_tags ) && count( $article_tags ) > 0 ) {
				$article_tags = array_unique( $article_tags );
				if ( false !== stripos( $footer, '{article_tags}' ) ) {
					$footer = str_replace( '{article_tags}', implode( ', ', $article_tags ), $footer );
				}
			}
			$this->output( $footer, $buffer );
		}
		$this->wpclilog( '--- END POST ----' );

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
			$this->wpclilog( '* Creating post with ' . $article_count . ' articles.' );
			// Do some sanity checks.
			// Call create_post here.
			$wp_insert_post_result = $this->create_post();
			if ( $wp_insert_post_result > 0 ) {
				$this->post_id_created = $wp_insert_post_result;
				$this->wpclilog( '* Created ' . $this->post_id_created . ' - "' . $this->post_title . '" on ' . $this->slug );
				// Append Tags if any set.
				if ( is_array( $post_tags ) && count( $post_tags ) > 0 ) {
					$set_tags = wp_set_post_tags( $this->post_id_created, $post_tags, false );
					if ( ! is_wp_error( $set_tags ) ) {
						$this->wpclilog( '* Set tags ' . implode( ', ', $post_tags ) );
					} else {
						$this->wpclierror( "*** Error - could not set the tags...\n" . $set_tags->get_error_message() );
					}
				} else {
					$this->wpcliwarn( '! No tags to set. (This will cause issues if you have no default tags in SteemPress set) ' . implode( ', ', $post_tags ) );
				}
				// Force the use of a --publish flag.
				if ( $do_publish ) {
					// Transition post to publish state.
					wp_publish_post( $this->post_id_created );
					$this->wpclilog( '* Post is now Published ' );

					if ( ! class_exists( 'Steempress_sp_Admin' ) ) {
						$this->wpcliwarn( '? SteemPress NOT available (did you install it?), can not post to steem. ' );
					} else {
						$this->wpclilog( '* SteemPress IS available, can post to steem, so trying that now ' );

						// Since we're using another plugin directly we'll try and catch whatever goes wrong.
						try {
							$test = new Steempress_sp_Admin( 'steempress_sp', '2.3' );
							$test->Steempress_sp_publish( $this->post_id_created );
							// Alt Steempress_sp_Admin::Steempress_sp_publish( $this->post_id_created);.
							$steempress_sp_permlink = get_post_meta( $this->post_id_created, 'steempress_sp_permlink' );
							$steempress_sp_author   = get_post_meta( $this->post_id_created, 'steempress_sp_author' );
							if ( ! empty( $steempress_sp_permlink ) && ! empty( $steempress_sp_author ) ) {
								$this->wpclilog( '* Posted to SteemPress API with: ' . $steempress_sp_author . ' / ' . $steempress_sp_permlink );
							} else {
								$this->wpcliwarn( '? SteemPress API post failed for some reason :-( ' );
							}
						} catch ( Exception $e ) {
							$this->wpclierror( '*** Error - SteemPress Call Blew up ' . $e->getMessage() );
						}
					}
				}
			} else {
				$this->wpclierror( '*** Error - could not create the post...\n' . $wp_insert_post_result->get_error_message() );
			}
		}

		return array(
			'post_title' => $this->post_title,
			'content'    => $this->content_buffer,
		);
	}

}
