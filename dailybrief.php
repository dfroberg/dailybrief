<?php
/**
 * Plugin Name: Daily Brief
 * Description: WP-CLI command plugin to generate a daily brief of todays posts.
 * Author:      Danny Froberg
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * GitLab Plugin URI: https://gitlab.froberg.org/dfroberg/dailybrief
 * Version: 0.0.6
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
            $this->date_suffix      = '';
	        $this->content_buffer   = "";
	        $this->temp_featured_image_url = "";
	        $this->post_id_created  = 0;

            $this->options          = get_option( 'dailybrief_options', array());
            $this->debug            = $this->get_option_default("debug",0); // 1 for on
            $this->include_toc      = $this->get_option_default("include_toc",1); // 1 for on / 0 for off
            $this->include_toc_localhrefs
                                    = $this->get_option_default("include_toc_localhrefs",1); // 1 for on / 0 for off
            $this->url_suffix       = $this->get_option_default("url_suffix",''); // set '?campaign=steempress&utm=dailybrief'
            $this->excerpt_words    = $this->get_option_default("excerpt_words",100);
            $this->post_title       = $this->get_option_default("post_title","The Daily Brief").' '.$this->date_suffix;
            $this->author_id        = $this->get_option_default("author_id",1);
            $this->post_category    = $this->get_option_default("post_category",1); // 1,2,8
            $this->post_tags        = $this->get_option_default("post_tags",''); // life,blog,news
            $this->always_skip_category
                                    = $this->get_option_default("always_skip_category",$this->post_category); // Always skip the category of Daily Brief Posts
            $this->slug             = $this->get_option_default("slug","the-daily-brief").'-'.$this->date_suffix;
            $this->comment_status   = $this->get_option_default("comment_status",'open');
            $this->ping_status      = $this->get_option_default("ping_status",'closed');
            $this->post_status      = $this->get_option_default("post_status",'publish');
            $this->post_type        = $this->get_option_default("post_type",'post');
            $this->article_delimiter= $this->get_option_default("article_delimiter",'<hr>');
            $this->article_continue = $this->get_option_default("article_continue",'Continue&nbsp;-&gt;');
            $this->article_stats_txt= $this->get_option_default("article_stats_txt",'<hr>Articles in this brief: ');
	        $this->article_stats_cats_txt
		                            = $this->get_option_default("article_stats_cats_txt",'<br>Categories in this brief: ');
            $this->article_stats_tags_txt
                                    = $this->get_option_default("article_stats_tags_txt",'<br>Tags in this brief: ');
	        $this->featured_image_url= $this->get_option_default("featured_image_url",'');

        }

        /**
         * Re applies the defaults with options
         */
        private function update_globals() {
	        $this->options          = get_option( 'dailybrief_options', array());
	        $this->debug            = $this->get_option_default("debug",0); // 1 for on
            $this->include_toc      = $this->get_option_default("include_toc",1); // 1 for on / 0 for off
            $this->include_toc_localhrefs
                                    = $this->get_option_default("include_toc_localhrefs",1); // 1 for on / 0 for off
	        $this->url_suffix       = $this->get_option_default("url_suffix",''); // set '?campaign=steempress&utm=dailybrief'
	        $this->excerpt_words    = $this->get_option_default("excerpt_words",100);
	        $this->post_title       = $this->get_option_default("post_title","The Daily Brief").' '.$this->date_suffix;
	        $this->author_id        = $this->get_option_default("author_id",1);
	        $this->post_category    = $this->get_option_default("post_category",1); // 1,2,8
            $this->post_tags        = $this->get_option_default("post_tags",''); // life,blog,news
	        $this->always_skip_category
		                            = $this->get_option_default("always_skip_category",$this->post_category); // Always skip the category of Daily Brief Posts
	        $this->slug             = $this->get_option_default("slug","the-daily-brief").'-'.$this->date_suffix;
	        $this->comment_status   = $this->get_option_default("comment_status",'open');
	        $this->ping_status      = $this->get_option_default("ping_status",'closed');
	        $this->post_status      = $this->get_option_default("post_status",'publish');
	        $this->post_type        = $this->get_option_default("post_type",'post');
	        $this->article_delimiter= $this->get_option_default("article_delimiter",'<hr>');
	        $this->article_continue = $this->get_option_default("article_continue",'Continue&nbsp;-&gt;');
	        $this->article_stats_txt= $this->get_option_default("article_stats_txt",'<hr>Articles in this brief: ');
            $this->article_stats_cats_txt
                                    = $this->get_option_default("article_stats_cats_txt",'<br>Categories in this brief: ');
            $this->article_stats_tags_txt
                                    = $this->get_option_default("article_stats_tags_txt",'<br>Tags in this brief: ');
	        $this->featured_image_url= $this->get_option_default("featured_image_url",'');
        }

        /**
         * Prepare for buffering output to a new post
         *
         * @param $output
         * @param bool $buffer
         */
        private function output( $output, $buffer = false ) {
            if($buffer == false) {
                WP_CLI::log($output);
            } else {
                $this->content_buffer .= $output;
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
                    'post_content'      =>   ($this->content_buffer),
                    'post_status'       =>   'draft',
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
         *      wp dailybrief set header '<h1>This is the header, this summary contains {article_count} articles about {article_categories}.</h1>'
         *      wp dailybrief set footer '<h1>This is the footer.</h1>'
         *      wp dailybrief set post_title 'The Your Site Daily Brief'
         *      wp dailybrief set post_status 'draft'
         *      wp dailybrief set post_tags 'news-blog,life,photography'
         *
         * @param $args
         * @param $assoc_args
         */
        public function set( $args, $assoc_args ) {
            $option_name = $args[0];  // value: "arg1"
            $option_value = $args[1]; // value: 42

            if ( !empty($this->options) ) {
                $this->options[$option_name] = $option_value;
                // The option already exists, so we just update it.
                update_option( 'dailybrief_options', $this->options );
                WP_CLI::log( 'Updated '.$option_name.' = '.$option_value );
            } else {
                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                $deprecated = null;
                $autoload = 'no';
                $this->options[$option_name] = $option_value;
                add_option( 'dailybrief_options', $this->options, $deprecated, $autoload );
                WP_CLI::log( 'Added '.$option_name.' = '.$option_value );
            }
        }

        /**
         * Runs some tests and output debug values, mostly intended for development
         *
         * ## OPTIONS
         *
         * [--days=<days>]
         * : Days back from where to get the posts to summarize 'today' / '-1 day' / '-2 days'
         * ---
         * default: today
         * ---
         * @param $args
         * @param $assoc_args
         */
        public function test( $args, $assoc_args ) {

            WP_CLI::log( '=== Testing ===' );
	        $days = WP_CLI\Utils\get_flag_value($assoc_args, 'days', 'today' );
	        $split = WP_CLI\Utils\get_flag_value($assoc_args, 'split', 3 );

            $today = strtotime($days);
            $tomorrow = strtotime("+1 day",$today);
            $today = date('Y-m-d H:m:s',$today);
            $tomorrow = date('Y-m-d H:m:s',$tomorrow);
	        $this->date_suffix = $today; // used for post-title & slug suffix, contains the date it relates to.

            WP_CLI::log( 'Today: '.$today);
            WP_CLI::log( 'Tomorrow: '. $tomorrow);
            WP_CLI::log( 'Day is set to :'. $this->date_suffix);

            WP_CLI::log( print_r($this->options,true) );
			$page = 1;
	        $before_date = $today;
	        $after_date = $today;
	        $exclude_posts = array();
	        $failed_posts = array();
	        $exclude_categories = array();
	        $status = array( 'publish' );
	        $types = array( 'post' );
	        do {
		        $query = new WP_Query( array(
			        'posts_per_page' => $split,
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
			        $title = $query->post->post_title;
			        $date = $query->post->post_date;
			        WP_CLI::log( '* '.$date.' - '.$title.'');

		        }
		        $page++;
	        } while ( $query->have_posts() );

        }

        /**
         * Create list of posts with dates between before and after dates
         *
         * ## OPTIONS
         *
         * [--post]
         * : Create the post in Wordpress
         *
         * [--use-excerpts]
         * : Do you want to use the excepts of the summarized Wordpress posts
         * ---
         * default: true
         * ---
         *
         * [--publish]
         * : Set the post_status to 'Publish' Wordpress posts
         * ---
         * default: false
         * ---
         *
         * [--days=<days>]
         * : Days back from where to get the posts to summarize 'today' / '-1 day' / '-2 days'
         * ---
         * default: today
         * ---
         * ### Examples:
         *  To dump an preview to the console;
         *    wp dailybrief create --days="-1 day" --no-use-excerpts
         *
         *  To produce a draft post;
         *    wp dailybrief create --days="2018-10-15" --use-excerpts --post
         *
         *  To create and publish a post;
         *    wp dailybrief create --days="today" --post --publish
         *
         * @param 	$args
         * @param 	$assoc_args --skip-posts 	    Skip including specific posts 1,2,3,4
         * @param 	$assoc_args --skip-categories	Skip including specific categories
         * @param 	$assoc_args --days 	            Include posts from '-1 days' etc default is 'today'
         */
        public function create( $args, $assoc_args ) {
            global $wpdb;
	        $days = WP_CLI\Utils\get_flag_value($assoc_args, 'days', 'today' );
            $today = strtotime($days);
            $tomorrow = strtotime("+1 day",$today);
            $today = date('Y-m-d',$today);
            $tomorrow = date('Y-m-d',$tomorrow);
            $this->date_suffix = $today; // used for post-title & slug suffix, contains the date it relates to.
            $before_date = $today;
            $after_date = $today;
            $exclude_posts = array();
            $failed_posts = array();
            $exclude_categories = array();
            $status = array( 'publish' );
            $types = array( 'post' );
            $buffer = false ;

            // Unfurl tags if any
            if(strlen($this->post_tags) > 0)
                $this->post_tags = explode(',',$this->post_tags);

			// Parse some flags
	        $post = WP_CLI\Utils\get_flag_value($assoc_args, 'post', false );
	        if ($post) {
	            // Ok prepare the post
                $buffer = true;
                WP_CLI::log( '* Preparing post for '.$today );
            }
	        // Use excerpts or not
            $use_excerpts = WP_CLI\Utils\get_flag_value($assoc_args, 'use-excerpts', true );
	        // Do you wish to focus on a particular category?
	        $focus = @explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'focus', '' ));
	        // Exclude some post_ids for whatever reason
	        $exclude_posts = @explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'skip-posts', '' ));
	        // Exclude some category ids for whatever reason and merge with the always_skip_category option
	        $exclude_categories = array_merge(@explode(',', WP_CLI\Utils\get_flag_value($assoc_args, 'skip-categories', '' )),@explode(',',$this->always_skip_category));
            // Parse some flags
            $include_stats = WP_CLI\Utils\get_flag_value($assoc_args, 'stats', true );
            $do_publish = WP_CLI\Utils\get_flag_value($assoc_args, 'publish', false );
            // Retrieve posts
            $page = 1;
            $article_count = 0;
            $article = "";
            $toc_items = '';
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
                    $more = '... <a href="'.get_permalink( $id).'" target="dailybrief">'.$this->article_continue.'</a>';

                    if ( ! has_excerpt() || ! $use_excerpts) {
                        $excerpt =  wp_trim_words( wp_strip_all_tags($content,true), $this->excerpt_words, $more);
                    } else {
                        $excerpt =  wp_trim_words( wp_strip_all_tags( get_the_excerpt($query),true), $this->excerpt_words, $more);
                    }
                    $title = $query->post->post_title;
                    $date = $query->post->post_date;
                    // Add any attachments on this post to the list of excluded attachments if this post is excluded
                    if ( in_array( $id, $exclude_posts ) )
                            continue;
                    // Spit out some posts
                    $article_count++;

                    // Get article categories for stats
                    $c = get_the_category($id);
                    $c_cats = array();
                    if ($c) {
                        foreach ($c as $c_cat) {
                            $c_cats[] = ucwords($c_cat->category_nicename,"- \t\r\n\f\v");
                            $article_category = ucwords($c_cat->category_nicename,"- \t\r\n\f\v");
                            $article_categories[$article_category] = $article_category;
                        }
                    }

                    // Get the article tags for stats
                    $t = get_the_tags($id);
                    $t_tags = array();
                    if ($t) {
                        foreach ($t as $t_tag) {
                            $t_tags[] = ucwords($t_tag->name,"- \t\r\n\f\v");
                            $article_tag = ucwords($t_tag->name,"- \t\r\n\f\v");
                            $article_tags[$article_tag] = $article_tag;
                        }
                    }
                    // Pick a temporary featured image from the posts in the brief to use if featured_image_url is not set.
                    if($this->temp_featured_image_url == '' && $this->featured_image_url == '')
	                    $this->temp_featured_image_url = get_the_post_thumbnail_url($id, 'full');

                    // Compile a TOC
                    if($this->include_toc == 1) {
                        $toc_items .= '<li>';
                        if ($this->include_toc_localhrefs == 1)
                            $toc_items .= '<a href="#author_permlink' . $id . '">';
                        $toc_items .= $title . '</a></li>';
                    }

                    if($this->include_toc_localhrefs == 1)
                        $article .= ( '<a id="author_permlink'.$id.'" name="author_permlink'.$id.'"></a>');
                    $article .= ( '<img src="'.get_the_post_thumbnail_url($id, 'full').'">');
                    $article .= ( '<h2><a href="'.get_permalink( $id).$this->url_suffix.'" target="dailybrief">'.$title.'</a></h2>');
                    $article .= ( 'Published <strong>'.$date.'</strong> by <strong>'.get_the_author().'</strong> in <strong>'.implode(', ',$c_cats).'</strong>' );
                    $article .= ( '<p>'.$excerpt.'</p>' );
                    $article .= ( '<p>Tags: '.implode(', ',$t_tags).'</p>' );
                    $article .= ( $this->article_delimiter);
                    WP_CLI::log( '+ Added: '.$title );
                }
            $page++;
            } while ( $query->have_posts() );
            // End of post preparation

	        // Ouputs
            WP_CLI::log( "--- BEGIN POST ----");
	        // Output Header
	        if(!empty($this->options['header'])) {
	        	$header = $this->options['header'];
		        if($include_stats) {
		            if(stristr($header,'{article_count}')) {
                        $header = str_replace('{article_count}', $article_count, $header);
                    } else {
                        $stats = $this->article_stats_txt . ' ' . $article_count;
                    }

			        if(is_array($article_categories) && count($article_categories) > 0) {
                        if(stristr($header,'{article_categories}')) {
                            $header = str_replace('{article_categories}', implode(", ",$article_categories), $header);
                        } else {
                            @$stats .= $this->article_stats_cats_txt.' '.implode(", ",$article_categories);
                        }
                    }

                    if(is_array($article_tags) && count($article_tags) > 0) {
                        if(stristr($header,'{article_tags}')) {
                            $header = str_replace('{article_tags}', implode(", ",$article_tags), $header);
                        } else {
                            @$stats .= $this->article_stats_tags_txt.' '.implode(", ",$article_tags);
                        }
                    }
			        $header .= $stats;
		        }

		        $this->output( $header, $buffer );
	        }
	        // Output optional TOC
            if($this->include_toc == 1) {
                $this->output( '<hr><p><h3>Table of Contents</h3><ul>',$buffer );
                $this->output( $toc_items, $buffer );
                $this->output( '</ul></p><hr>',$buffer );
            }

	        // Output article
	        $this->output( $article,$buffer );

	        // Output Footer
	        if(!empty($this->options['footer']))
		        $this->output( $this->options['footer'],$buffer );

            WP_CLI::log( "--- END POST ----");

	        // Create WP Post
            if ($post && $article_count > 0) {
            	// Update the globals to recreate slugs and titles etc if anything changed via args
	            $this->update_globals();
                // Ok create the post
                WP_CLI::log( '* Creating post with '.$article_count.' articles.' );
                // Do some sanity checks

                // Call create_post here
	            $wp_insert_post_restult = $this->create_post();
	            if($wp_insert_post_restult > 0) {
                    $this->post_id_created = $wp_insert_post_restult;
		            WP_CLI::log( '* Created ' . $this->post_id_created .' - "'.$this->post_title.'" on '.$this->slug);
                    // Append Tags if any set
                    if(is_array($this->post_tags) && count($this->post_tags) > 0) {
                        $settags = wp_set_post_tags($this->post_id_created, $this->post_tags, false);
                        if (!is_wp_error($settags)) {
                            WP_CLI::log('* Set tags ' . @implode(', ', $this->post_tags));
                        } else {
                            WP_CLI::error("*** Error - could not set the tags...\n" . $settags->get_error_message());
                        }
                    } else {
                        WP_CLI::warning('! No tags to set. (This will cause issues if you have no default tags in SteemPress set)');
                    }
                    // WIP: This is a test
                    $value = get_post_meta($this->post_id_created, 'Steempress_sp_steem_publish', true);
                    if ($value == "0") {
                        if(update_post_meta($this->post_id_created, 'Steempress_sp_steem_publish', true)) {
                            WP_CLI::log( '* Updated SeemPress meta' );
                        } else {
                            WP_CLI::log( '- Could not update SeemPress meta' );
                        }
                    } else {
                        WP_CLI::log( '? Got already set SeemPress meta '.$value );
                    }
                    // Force the use of a --publish flag
		            if($do_publish) {
			            wp_publish_post( $this->post_id_created );
                        WP_CLI::log( '* Transitioning to Publish state ' );
		            }
	            } else {
		            WP_CLI::error( "*** Error - could not create the post...\n". $wp_insert_post_restult->get_error_message());
	            }
            }
        }
    }

    // Finally add the command to WP_CLI
    WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
}
