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
	 * This contains the created WP Post ID if sucessfully generated.
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
	 * @var array
	 */
	private $debug;
	/**
	 * Shall we include the Table of Contents in the brief?
	 *
	 * @var integer
	 */
	private $include_toc;
	/**
	 * Due to a bug in Steem Condenser and many other condensers, do we want to make local anchor HREFs to the articles?
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
	 * @var string
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
	 * @var array
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
	 * @var array
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

	}

	/**
	 * Re applies the defaults with options
	 */
	public function update_globals() {
		$this->options                 = get_option( $this->plugin_name /* 'dailybrief_options' */, array() );
		$this->debug                   = $this->get_option_default( 'debug', 0 ); // 1 for on
		$this->include_toc             = $this->get_option_default( 'include_toc', 1 ); // 1 for on / 0 for off
		$this->include_toc_local_hrefs = $this->get_option_default( 'include_toc_local_hrefs', 1 ); // 1 for on / 0 for off
		$this->url_suffix              = $this->get_option_default( 'url_suffix', '' ); // set '?campaign=steempress&utm=dailybrief'.
		$this->excerpt_words           = $this->get_option_default( 'excerpt_words', 100 );
		$this->post_title              = $this->get_option_default( 'post_title', 'The Daily Brief' ) . ' ' . $this->date_suffix;
		$this->author_id               = $this->get_option_default( 'author_id', 1 );
		$this->post_category           = $this->get_option_default( 'post_category', 1 ); // 1,2,8
		$this->post_tags               = $this->get_option_default( 'post_tags', '' ); // life,blog,news.
		$this->always_skip_category    = $this->get_option_default( 'always_skip_category', $this->post_category ); // Always skip the category of Daily Brief Posts.
		$this->always_skip_tags        = $this->get_option_default( 'always_skip_tag', 0 );
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
	}

	/**
	 * Return an dailybrief_options value if exists otherwise return the default.
	 *
	 * @param string $option option name.
	 * @param mixed  $default default value to return.
	 *
	 * @return mixed
	 */
	private function get_option_default( $option, $default ) {
		if ( ! isset( $this->options[ $option ] ) ) {
			return $default;
		}

		return $this->options[ $option ];
	}

	/**
	 * Creates a new WordPress post with all the specified arguments set by options
	 *
	 * @return mixed post_id
	 */
	private function create_post() {
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
		if ( false === $dailybrief_featured_image_id ) {
			WP_CLI::warning( 'Unable to set featured image, make sure you have uploaded the image you want to use to your sites media library and set the featured_image_url option with its complete URL.' );

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

}
