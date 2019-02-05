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
	 * @return string
	 */
	public function getDateSuffix(): string {
		return $this->date_suffix;
	}

	/**
	 * @param string $date_suffix
	 */
	public function setDateSuffix( string $date_suffix ): void {
		$this->date_suffix = $date_suffix;
	}

	/**
	 * @return string
	 */
	public function getTempFeaturedImageUrl(): string {
		return $this->temp_featured_image_url;
	}

	/**
	 * @param string $temp_featured_image_url
	 */
	public function setTempFeaturedImageUrl( string $temp_featured_image_url ): void {
		$this->temp_featured_image_url = $temp_featured_image_url;
	}

	/**
	 * @return int
	 */
	public function getPostIdCreated(): int {
		return $this->post_id_created;
	}

	/**
	 * @param int $post_id_created
	 */
	public function setPostIdCreated( int $post_id_created ): void {
		$this->post_id_created = $post_id_created;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array {
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions( array $options ): void {
		$this->options = $options;
	}

	/**
	 * @return array
	 */
	public function getDebug(): array {
		return $this->debug;
	}

	/**
	 * @param array $debug
	 */
	public function setDebug( array $debug ): void {
		$this->debug = $debug;
	}

	/**
	 * @return int
	 */
	public function getIncludeToc(): int {
		return $this->include_toc;
	}

	/**
	 * @param int $include_toc
	 */
	public function setIncludeToc( int $include_toc ): void {
		$this->include_toc = $include_toc;
	}

	/**
	 * @return int
	 */
	public function getIncludeTocLocalHrefs(): int {
		return $this->include_toc_local_hrefs;
	}

	/**
	 * @param int $include_toc_local_hrefs
	 */
	public function setIncludeTocLocalHrefs( int $include_toc_local_hrefs ): void {
		$this->include_toc_local_hrefs = $include_toc_local_hrefs;
	}

	/**
	 * @return int
	 */
	public function getUrlSuffix(): int {
		return $this->url_suffix;
	}

	/**
	 * @param int $url_suffix
	 */
	public function setUrlSuffix( int $url_suffix ): void {
		$this->url_suffix = $url_suffix;
	}

	/**
	 * @return string
	 */
	public function getExcerptWords(): string {
		return $this->excerpt_words;
	}

	/**
	 * @param string $excerpt_words
	 */
	public function setExcerptWords( string $excerpt_words ): void {
		$this->excerpt_words = $excerpt_words;
	}

	/**
	 * @return int
	 */
	public function getPostTitle(): int {
		return $this->post_title;
	}

	/**
	 * @param int $post_title
	 */
	public function setPostTitle( int $post_title ): void {
		$this->post_title = $post_title;
	}

	/**
	 * @return string
	 */
	public function getAuthorId(): string {
		return $this->author_id;
	}

	/**
	 * @param string $author_id
	 */
	public function setAuthorId( string $author_id ): void {
		$this->author_id = $author_id;
	}

	/**
	 * @return int
	 */
	public function getPostCategory(): int {
		return $this->post_category;
	}

	/**
	 * @param int $post_category
	 */
	public function setPostCategory( int $post_category ): void {
		$this->post_category = $post_category;
	}

	/**
	 * @return int
	 */
	public function getPostTags(): int {
		return $this->post_tags;
	}

	/**
	 * @param int $post_tags
	 */
	public function setPostTags( int $post_tags ): void {
		$this->post_tags = $post_tags;
	}

	/**
	 * @return array
	 */
	public function getAlwaysSkipCategory(): array {
		return $this->always_skip_category;
	}

	/**
	 * @param array $always_skip_category
	 */
	public function setAlwaysSkipCategory( array $always_skip_category ): void {
		$this->always_skip_category = $always_skip_category;
	}

	/**
	 * @return int
	 */
	public function getAlwaysSkipTags(): int {
		return $this->always_skip_tags;
	}

	/**
	 * @param int $always_skip_tags
	 */
	public function setAlwaysSkipTags( int $always_skip_tags ): void {
		$this->always_skip_tags = $always_skip_tags;
	}

	/**
	 * @return array
	 */
	public function getSlug(): array {
		return $this->slug;
	}

	/**
	 * @param array $slug
	 */
	public function setSlug( array $slug ): void {
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function getCommentStatus(): string {
		return $this->comment_status;
	}

	/**
	 * @param string $comment_status
	 */
	public function setCommentStatus( string $comment_status ): void {
		$this->comment_status = $comment_status;
	}

	/**
	 * @return string
	 */
	public function getPingStatus(): string {
		return $this->ping_status;
	}

	/**
	 * @param string $ping_status
	 */
	public function setPingStatus( string $ping_status ): void {
		$this->ping_status = $ping_status;
	}

	/**
	 * @return string
	 */
	public function getPostStatus(): string {
		return $this->post_status;
	}

	/**
	 * @param string $post_status
	 */
	public function setPostStatus( string $post_status ): void {
		$this->post_status = $post_status;
	}

	/**
	 * @return string
	 */
	public function getPostType(): string {
		return $this->post_type;
	}

	/**
	 * @param string $post_type
	 */
	public function setPostType( string $post_type ): void {
		$this->post_type = $post_type;
	}

	/**
	 * @return string
	 */
	public function getArticleDelimiter(): string {
		return $this->article_delimiter;
	}

	/**
	 * @param string $article_delimiter
	 */
	public function setArticleDelimiter( string $article_delimiter ): void {
		$this->article_delimiter = $article_delimiter;
	}

	/**
	 * @return string
	 */
	public function getArticleContinue(): string {
		return $this->article_continue;
	}

	/**
	 * @param string $article_continue
	 */
	public function setArticleContinue( string $article_continue ): void {
		$this->article_continue = $article_continue;
	}

	/**
	 * @return string
	 */
	public function getArticleStatsTxt(): string {
		return $this->article_stats_txt;
	}

	/**
	 * @param string $article_stats_txt
	 */
	public function setArticleStatsTxt( string $article_stats_txt ): void {
		$this->article_stats_txt = $article_stats_txt;
	}

	/**
	 * @return string
	 */
	public function getArticleStatsCatsTxt(): string {
		return $this->article_stats_cats_txt;
	}

	/**
	 * @param string $article_stats_cats_txt
	 */
	public function setArticleStatsCatsTxt( string $article_stats_cats_txt ): void {
		$this->article_stats_cats_txt = $article_stats_cats_txt;
	}

	/**
	 * @return string
	 */
	public function getArticleStatsTagsTxt(): string {
		return $this->article_stats_tags_txt;
	}

	/**
	 * @param string $article_stats_tags_txt
	 */
	public function setArticleStatsTagsTxt( string $article_stats_tags_txt ): void {
		$this->article_stats_tags_txt = $article_stats_tags_txt;
	}

	/**
	 * @return string
	 */
	public function getFeaturedImageUrl(): string {
		return $this->featured_image_url;
	}

	/**
	 * @param string $featured_image_url
	 */
	public function setFeaturedImageUrl( string $featured_image_url ): void {
		$this->featured_image_url = $featured_image_url;
	}

	/**
	 * @return string
	 */
	public function getContentBuffer(): string {
		return $this->content_buffer;
	}

	/**
	 * @param string $content_buffer
	 */
	public function setContentBuffer( string $content_buffer ): void {
		$this->content_buffer = $content_buffer;
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
	public function create_post() {
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

	/**
	 * Set option parameter
	 *
	 * @param string $option_name Name of option to set.
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

	public function wpclilog( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( $message );
		}
	}

	public function wpcliwarn( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::warning( $message );
		}
	}

	public function wpclierror( $message ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( $message );
		}
	}

	/**
	 * Prepare for buffering output to a new post
	 *
	 * @param string $output what to write.
	 * @param bool $buffer log or stdout.
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
	 * Parse argument array
	 *
	 * @param array $arguments Array of arguments sent to function.
	 * @param string $parameter Name of parameter to return.
	 * @param mixed $default Default value of the parameter.
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
	 */
	public function create( $arguments ) {
		global
		$wpdb;

		$days     = $this->parse_arguments( $arguments, 'days', 'today' );
		$today    = strtotime( $days );
		$tomorrow = strtotime( '+1 day', $today );
		$today    = date( 'Y-m-d', $today );
		$tomorrow = date( 'Y-m-d', $tomorrow );
		$this->setDateSuffix( $today ); // used for post-title & slug suffix, contains the date it relates to.
		$before_date = $today;
		$after_date  = $today;
		// Exclude some category ids for whatever reason and merge with the always_skip_category option.
		$skip_categories = $this->parse_arguments( $arguments, 'skip-categories', '' );
		if ( ! empty( $skip_categories ) ) {
			$exclude_categories = array_merge( explode( ',', $skip_categories ), $this->getAlwaysSkipCategory() );
		}
		// Exclude some tag ids for whatever reason and merge with the always_skip_tags option.
		$skip_tags = $this->parse_arguments( $arguments, 'skip-tags', '' );
		if ( ! empty( $skip_tags ) ) {
			$exclude_tags = array_merge( explode( ',', $skip_tags ), $this->getAlwaysSkipTags() );
		}
		// Exclude some post_ids for whatever reason.
		$skip_posts = $this->parse_arguments( $arguments, 'skip-posts', '' );
		if ( ! empty( $skip_posts ) ) {
			$exclude_posts = explode( ',', $skip_posts );
		}

		$status = array( 'publish' );
		$types  = array( 'post' );
		$buffer = false;

		// Parse some flags.
		$post = $this->parse_arguments( $arguments, 'post', false );
		if ( $post ) {
			// Ok prepare the post.
			$buffer = true;
			$this->wpclilog( '* Preparing post for ' . $today );
		}
		// Use excerpts or not.
		$use_excerpts = $this->parse_arguments( $arguments, 'use-excerpts', true );
		// Do you wish to focus on a particular category?
		$focus = $this->parse_arguments( $arguments, 'focus', '' );
		if ( ! empty( $focus ) ) {
			$focus = explode( ',', $focus );
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
				$more    = '... <a href="' . get_permalink( $id ) . $this->getUrlSuffix() . '" target="dailybrief">' . $this->getArticleContinue() . '</a>';

				if ( ! $use_excerpts || ! has_excerpt() ) {
					$excerpt = wp_trim_words( wp_strip_all_tags( $content, true ), $this->getExcerptWords(), $more );
				} else {
					$excerpt = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $query ), true ), $this->getExcerptWords(), $more );
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
				if ( '' === $this->getTempFeaturedImageUrl() && '' === $this->getFeaturedImageUrl() ) {
					$this->setTempFeaturedImageUrl( get_the_post_thumbnail_url( $id, 'full' ) );
				}
				// Compile a TOC.
				if ( 1 === $this->getIncludeToc() ) {
					$toc_items .= '<li>';
					if ( 1 === $this->getIncludeTocLocalHrefs() ) {
						$toc_items .= '<a href="#_author_permlink_' . $id . '">';
					}
					$toc_items .= $title . '</a></li>';
				}

				if ( 1 === $this->getIncludeTocLocalHrefs() ) {
					$article .= ( '<a id="_author_permlink_' . $id . '" name="_author_permlink_' . $id . '"></a>' );
				}
				$article .= ( '<img src="' . get_the_post_thumbnail_url( $id, 'full' ) . '">' );
				$article .= ( '<h2><a href="' . get_permalink( $id ) . $this->url_suffix . '" target="dailybrief">' . $title . '</a></h2>' );
				$article .= ( 'Published <strong>' . $date . '</strong> by <strong>' . get_the_author() . '</strong> in <strong>' . implode( ', ', $c_cats ) . '</strong>' );
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
							// Alt Steempress_sp_Admin::Steempress_sp_publish($this->post_id_created);.
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
	}

}
