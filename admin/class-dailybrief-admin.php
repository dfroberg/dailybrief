<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.froberg.org
 * @since      1.0.0
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dailybrief
 * @subpackage Dailybrief/admin
 * @author     Daniel Froberg <danny@froberg.org>
 */
class Dailybrief_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dailybrief_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dailybrief_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dailybrief-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dailybrief_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dailybrief_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/dailybrief-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			'DailyBrief Options',
			'DailyBrief',
			'manage_options',
			$this->plugin_name,
			array(
				$this,
				'display_plugin_setup_page',
			)
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once( 'partials/dailybrief-admin-display.php' );
	}

	/**
	 * Update settings from plugins options page.
	 *
	 * @since    1.0.0
	 */
	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name, array( $this, 'validate' ) );
	}

	/**
	 * Validate settings from plugins options page.
	 *
	 * @since    1.0.0
	 *
	 * @param array $input un-sanitised variables.
	 *
	 * @return array
	 */
	public function validate( $input ) {
		// TODO: Instead of repeating defaults here, read them in from the main class and we'll do that everywhere. Also if a option is set with a value already use that as defaults instead.
		$valid                            = array();
		$valid['debug']                   = ( isset( $input['debug'] ) && ! empty( $input['debug'] ) ) ? htmlspecialchars( $input['debug'], ENT_QUOTES ) : '0';
		$valid['include_toc']             = ( isset( $input['include_toc'] ) && ! empty( $input['include_toc'] ) ) ? htmlspecialchars( $input['include_toc'], ENT_QUOTES ) : '0';
		$valid['include_toc_local_hrefs'] = ( isset( $input['include_toc_local_hrefs'] ) && ! empty( $input['include_toc_local_hrefs'] ) ) ? htmlspecialchars( $input['include_toc_local_hrefs'], ENT_QUOTES ) : '0';
		$valid['footer']                  = ( isset( $input['footer'] ) && ! empty( $input['footer'] ) ) ? ( $input['footer'] ) : '';
		$valid['header']                  = ( isset( $input['header'] ) && ! empty( $input['header'] ) ) ? ( $input['header'] ) : '';
		$valid['author_id']               = ( isset( $input['author_id'] ) && ! empty( $input['author_id'] ) ) ? ( $input['author_id'] ) : '';
		$valid['post_category']           = ( isset( $input['post_category'] ) && ! empty( $input['post_category'] ) ) ? ( $input['post_category'] ) : '';
		$valid['post_title']              = ( isset( $input['post_title'] ) && ! empty( $input['post_title'] ) ) ? ( $input['post_title'] ) : '';
		$valid['post_tags']               = ( isset( $input['post_tags'] ) && ! empty( $input['post_tags'] ) ) ? ( $input['post_tags'] ) : '';
		$valid['url_suffix']              = ( isset( $input['url_suffix'] ) && ! empty( $input['url_suffix'] ) ) ? ( $input['url_suffix'] ) : '?campaign=steempress&amp;utm=dailybrief';
		$valid['excerpt_words']           = ( isset( $input['excerpt_words'] ) && ! empty( $input['excerpt_words'] ) ) ? ( $input['excerpt_words'] ) : '100';
		$valid['slug']                    = ( isset( $input['slug'] ) && ! empty( $input['slug'] ) ) ? ( $input['slug'] ) : 'the-daily-brief';
		$valid['comment_status']          = ( isset( $input['comment_status'] ) && ! empty( $input['comment_status'] ) ) ? ( $input['comment_status'] ) : 'open';
		$valid['ping_status']             = ( isset( $input['ping_status'] ) && ! empty( $input['ping_status'] ) ) ? ( $input['ping_status'] ) : 'closed';
		$valid['article_delimiter']       = ( isset( $input['article_delimiter'] ) && ! empty( $input['article_delimiter'] ) ) ? ( $input['article_delimiter'] ) : '<hr>';
		$valid['article_continue']        = ( isset( $input['article_continue'] ) && ! empty( $input['article_continue'] ) ) ? htmlspecialchars( $input['article_continue'] ) : 'Continue->';
		$valid['article_stats_txt']       = ( isset( $input['article_stats_txt'] ) && ! empty( $input['article_stats_txt'] ) ) ? ( $input['article_stats_txt'] ) : '<hr>Articles in this brief: ';
		$valid['article_stats_cats_txt']  = ( isset( $input['article_stats_cats_txt'] ) && ! empty( $input['article_stats_cats_txt'] ) ) ? ( $input['article_stats_cats_txt'] ) : '<br>Categories in this brief:';
		$valid['featured_image_url']      = ( isset( $input['featured_image_url'] ) && ! empty( $input['featured_image_url'] ) ) ? ( $input['featured_image_url'] ) : '';
		$valid['article_stats_tags_txt']  = ( isset( $input['article_stats_tags_txt'] ) && ! empty( $input['article_stats_tags_txt'] ) ) ? ( $input['article_stats_tags_txt'] ) : '<br>Tags in this brief: ';
		$valid['featured_image_url']      = ( isset( $input['featured_image_url'] ) && ! empty( $input['featured_image_url'] ) ) ? ( $input['featured_image_url'] ) : '';

		return $valid;
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 *
	 * @param array $links plugin page link array to inject settings link into.
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', 'dailybrief' ) . '</a>',
		);

		return array_merge( $settings_link, $links );

	}

}
