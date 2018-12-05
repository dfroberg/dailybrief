<?php
/**
 * Plugin Name: Daily Brief
 * Description: WP-CLI command plugin to generate a daily brief of todays posts.
 * Author:      Danny Froberg
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * GitLab Plugin URI: https://gitlab.froberg.org/dfroberg/dailybrief
 * Version: 0.0.5
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Only accessible from WP-CLI
if ( defined('WP_CLI') && WP_CLI ) {
    
    class DailyBrief_CLI_Command extends WP_CLI_Command {

	    /**
	     * DailyBrief_CLI_Command constructor.
	     */
        public function __construct() {

            // constructor called when plugin loads
            $this->day              = '';
            $this->options          = get_option( 'dailybrief_options', array());
            $this->content_buffer   = "";
	        $this->temp_featured_image_url = "";
	        $this->excerpt_words    = $this->get_option_default("excerpt_words",100);
            $this->post_title       = $this->get_option_default("post_title","The Daily Brief ".$this->day);
            $this->author_id        = $this->get_option_default("author_id",1);
            $this->post_category    = $this->get_option_default("post_category",1); // 1,2,8
            $this->always_skip_category
                                    = $this->get_option_default("always_skip_category",$this->post_category); // Always skip the category of Daily Brief Posts
            $this->slug             = $this->get_option_default("slug","the-daily-brief-").$this->day;
            $this->comment_status   = $this->get_option_default("comment_status",'open');
            $this->ping_status      = $this->get_option_default("ping_status",'closed');
            $this->post_status      = $this->get_option_default("post_status",'publish');
            $this->post_type        = $this->get_option_default("post_type",'post');
	        $this->article_delimiter= $this->get_option_default("article_delimiter",'<hr>');
            $this->article_continue = $this->get_option_default("article_continue",'Continue&nbsp;-&gt;');
            $this->article_stats_txt= $this->get_option_default("article_stats_txt",'<hr>Articles in this brief: ');
	        $this->featured_image_url= $this->get_option_default("featured_image_url",'');
        }

        /**
         * Prepare for buffering output to a new post
         *
         * @param $output
         * @param bool $buffer
         */
        private function output( $output, $buffer = false ) {
            if($buffer === false) {
                WP_CLI::line($output);
            } else {
                $this->content_buffer += $output;
            }
        }

        /**
         * Return an dailybrief_options value if exists otherwise return the default.
         *
         * @param $option
         * @param $default
         * @return mixed
         */
        private function get_option_default($option,$default) {
            if(!isset($this->options[$option]))
                return $default;
            return $this->options[$option];
        }

        /**
         * Creates a new wordpress post with all the specified arguments set by options
         *
         * @return mixed post_id
         */
        private function create_post () {
            $post_id = wp_insert_post(
                array(
                    'comment_status'    =>   $this->comment_status,
                    'ping_status'       =>   $this->ping_status,
                    'post_author'       =>   $this->author_id,
                    'post_name'         =>   $this->slug,
                    'post_title'        =>   $this->post_title,
                    'post_content'      =>   wp_slash($this->content_buffer),
                    'post_status'       =>   $this->post_status,
                    'post_type'         =>   $this->post_type,
                    'post_category'     =>   @explode(',', $this->post_category )
                )
            );
            if($this->featured_image_url != '') {
	            $dailybrief_featured_image_id = attachment_url_to_postid( $this->featured_image_url );
            } else {
	            $dailybrief_featured_image_id = attachment_url_to_postid( $this->temp_featured_image_url );
            }
	        if($dailybrief_featured_image_id === false) {
		        WP_CLI::warning( 'Unable to set featured image, make sure you have uploaded the image you want to use to your sites media library and set the featured_image_url option with its complete URL.');
		        return $post_id;
	        }
	        // Set Featured image if available.
	        set_post_thumbnail( $post_id, $dailybrief_featured_image_id );
            return $post_id;
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
         *      wp dailybrief set header '<h1>This is the header.</h1>'
         *      wp dailybrief set footer '<h1>This is the footer.</h1>'
         *      wp dailybrief set post_title 'The Daily Brief'
         *
         * @param $args
         * @param $assoc_args
         */
        public function set( $args, $assoc_args ) {
            $option_name = $args[0];  // value: "arg1"
            $option_value = $args[1]; // value: 42

            //$option_name = $assoc_args['option'];
            //$option_value = $assoc_args['value'];


            if ( !empty($this->options) ) {
                $this->options[$option_name] = $option_value;
                // The option already exists, so we just update it.
                update_option( 'dailybrief_options', $this->options );
                WP_CLI::line( 'Updated '.$option_name.' = '.$option_value );
            } else {
                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'no';
                $this->options[$option_name] = $option_value;
                add_option( 'dailybrief_options', $this->options, $deprecated, $autoload );
                WP_CLI::line( 'Added '.$option_name.' = '.$option_value );
            }


        }

        /**
         * Runs some tests and output debug values, mostly intended for development
         *
         * @param $args
         * @param $assoc_args
         */
        public function test( $args, $assoc_args ) {
            $this->output( '=== Testing ===' );
            $days = $assoc_args['days'];
            if(is_null($days))
                $days = "today";
            $today = strtotime($days);
            $tomorrow = strtotime("+1 day",$today);
            $today = date('Y-m-d',$today);
            $tomorrow = date('Y-m-d',$tomorrow);

            $this->output( 'Today: '.$today);
            $this->output( 'Tomorrow: '. $tomorrow);


            $this->output( print_r($this->options,true) );

        }

        /**
         * Create list of posts with dates between before and after dates
         *
         *
         * @param 	$args
         * @param 	$assoc_args --skip-posts 	    Skip including specific posts 1,2,3,4
         * @param 	$assoc_args --skip-categories	Skip including specific categories
         * @param 	$assoc_args --days 	            Include posts from '-1 days' etc default is 'today'
         */
        public function brief( $args, $assoc_args ) {
            global $wpdb;
            $days = $assoc_args['days'];
            if(is_null($days))
                $days = "today";
            $today = strtotime($days);
            $tomorrow = strtotime("+1 day",$today);
            $today = date('Y-m-d',$today);
            $tomorrow = date('Y-m-d',$tomorrow);
            $this->day = $today; // used for post-title & slug suffix, contains the date it relates to.
            $before_date = $tomorrow;
            $after_date = $today;
            $exclude_posts = array();
            $failed_posts = array();
            $exclude_categories = array();
            $status = array( 'publish' );
            $types = array( 'post' );
            $buffer = false ;

			// Parse some flags
	        $post = WP_CLI\Utils\get_flag_value($assoc_args, 'post', false );
	        if ($post) {
	            // Ok prepare the post
                $buffer = true;
                WP_CLI::line( '* Preparing post' );
            }
	        // Do you wish to focus on a particular category?
	        $focus = @explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'focus', '' ));
	        // Exclude some post_ids for whatever reason
	        $exclude_posts = @explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'skip-posts', '' ));
	        // Exclude some category ids for whatever reason and merge with the always_skip_category option
	        $exclude_categories = array_merge(@explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'skip-categories', '' )),@explode(',',$this->always_skip_category));
            // Parse some flags
            $include_stats = WP_CLI\Utils\get_flag_value($assoc_args, 'stats', true );
            // Output Header
            if(!empty($this->options['header']))
                $this->output( $this->options['header'],$buffer );

            // Retrieve posts
            $page = 1;
            $article_count = 0;
            do {
                $query = new WP_Query( array(
                    'posts_per_page' => 30,
                    'paged' => $page,
                    'post_status' => $status,
                    'post_type' => $types,
                    'date_query' => array(
                        array(
                            'before' => $before_date,
                            'after' => $after_date,
                            'inclusive' => true,
                        ),
                    ),
                    'category__not_in' => $exclude_categories ,
                ) );

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $id = get_the_ID();
                    $content = $query->post->post_content;

                    if ( ! has_excerpt() ) {
                        $excerpt =  wp_trim_words( $content, $this->excerpt_words, '... <a href="'.get_permalink( $id).'" target="dailybrief">'.$this->article_continue.'</a>');
                    } else {
                        $excerpt =  the_excerpt();
                    }
                    $title = $query->post->post_title;
                    $date = $query->post->post_date;
                    // Add any attachments on this post to the list of excluded attachments if this post is excluded
                    if ( in_array( $id, $exclude_posts ) )
                            continue;
                    // Spit out some posts
                    $c = get_the_category();
                    $article_count++;
                    // Pick a temporary featured image from the posts in the brief to use if featured_image_url is not set.
                    if($this->temp_featured_image_url == '' && $this->featured_image_url == '')
	                    $this->temp_featured_image_url = get_the_post_thumbnail_url($id, 'full');

                    $this->output( '<img src="'.get_the_post_thumbnail_url($id, 'full').'">',$buffer);
                    $this->output( '<h2 id="'.$id.'"><a href="'.get_permalink( $id).'" target="dailybrief">'.$title.'</a></h2>',$buffer );
                    $this->output( 'Published <strong>'.$date.'</strong> by <strong>'.get_the_author().'</strong> in <strong>'.strtoupper($c[0]->category_nicename).'</strong>',$buffer );
                    $this->output( '<p>'.$excerpt.'</p>',$buffer );
                    $this->output( $this->article_delimiter, $buffer);
                }
            $page++;
            } while ( $query->have_posts() );

            // Output Footer
            if(!empty($this->options['footer']))
                $this->output( $this->options['footer'],$buffer );

            // Add stats
            if($include_stats)
                $this->output( $this->article_stats_txt.' '.$article_count ,$buffer );
            // End of post preparation

            if ($post) {
                // Ok create the post
                WP_CLI::line( '* Creating post with '.$article_count.' articles.' );
                // Do some sanity checks

                // Call create_post here
	            $post_id_created = $this->create_post();
	            if($post_id_created > 0) {
		            WP_CLI::line( '* Done ' . $post_id_created );
	            } else {
		            WP_CLI::error( '*** Error - could not create the post...');
	            }
            }
        }

    }



    // Finally add the command to WP_CLI
    WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
}
