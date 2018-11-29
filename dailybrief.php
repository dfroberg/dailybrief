<?php
/**
 * Plugin Name: Daily Brief
 * Description: WP-CLI command plugin to generate a daily brief of todays posts.
 * Author:      Danny Froberg
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * GitLab Plugin URI: https://gitlab.froberg.org/dfroberg/dailybrief
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

// Only accessible from WP-CLI
if ( defined('WP_CLI') && WP_CLI ) {
    
    class DailyBrief_CLI_Command extends WP_CLI_Command {

    function help( $args, $assoc_args ) {
        WP_CLI::line( '=== Daily Brief Help ===' );
        
    }

    public function set( $args, $assoc_args ) {
        $option_name = $args[0];  // value: "arg1"
        $option_value = $args[1]; // value: 42

        //$option_name = $assoc_args['option'];
        //$option_value = $assoc_args['value'];
        $options = get_option( 'dailybrief_options', array());

        if ( !empty($options) ) {
            $options[$option_name] = $option_value;
            // The option already exists, so we just update it.
            update_option( 'dailybrief_options', $options );
            WP_CLI::line( 'Updated '.$option_name.' = '.$option_value );
        } else {
            // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
            $deprecated = null;
            $autoload = 'no';
            $options[$option_name] = $option_value;
            add_option( 'dailybrief_options', $options, $deprecated, $autoload );
            WP_CLI::line( 'Added '.$option_name.' = '.$option_value );
        }


    }

    public function test( $args, $assoc_args ) {
        WP_CLI::line( '=== Testing ===' );
        $days = $assoc_args['days'];
        if(is_null($days))
            $days = "today";
        $today = strtotime($days);
        $tomorrow = strtotime("+1 day",$today);
        $today = date('Y-m-d',$today);
        $tomorrow = date('Y-m-d',$tomorrow);

        WP_CLI::line( 'Today: '.$today);
        WP_CLI::line( 'Tomorrow: '. $tomorrow);

        $options = get_option( 'dailybrief_options', array());
        WP_CLI::line( print_r($options,true) );

    }
    /**
     * Create list of posts with dates between before and after dates
     *
     * @example wp dialybrief posts
     * @param 	$args
     * @param 	$assoc_args --skip-posts 		Skip including specific posts
     * @param 	$assoc_args --skip-categories	Skip including specific categories
     * @param 	$assoc_args --days 	            Include posts from '-1 days' etc default is 'today'
     */
    public function posts( $args, $assoc_args ) {
        global $wpdb;
        $days = $assoc_args['days'];
        if(is_null($days))
            $days = "today";
        $today = strtotime($days);
        $tomorrow = strtotime("+1 day",$today);
        $today = date('Y-m-d',$today);
        $tomorrow = date('Y-m-d',$tomorrow);

        $before_date = $tomorrow;
        $after_date = $today;
        $exclude_posts = array();
        $failed_posts = array();
        $status = array( 'publish' );
        $types = array( 'post' );
        $options = get_option('dailybrief_options', array() );

        // Output Header
        if(!empty($options['header']))
            WP_CLI::line( $options['header'] );

        // Retrieve posts
        $page = 1;
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
            ) );

            while ( $query->have_posts() ) {
                $query->the_post();
                $id = get_the_ID();
                $content = $query->post->post_content;

                if ( ! has_excerpt() ) {
                    $excerpt =  wp_trim_words( $content, 100 , '... <a href="'.get_permalink( $id).'" target="dailybrief">Continue -&gt;</a>');
                } else {
                    $excerpt =  the_excerpt();
                }
                $title = $query->post->post_title;
                $date = $query->post->post_date;
                // Add any attachments on this post to the list of excluded attachments if this post is excluded
                if ( in_array( $id, $exclude_posts ) )
                        continue;
                // Spit out some posts
                WP_CLI::line( '<img src="'.get_the_post_thumbnail_url($id, 'full').'">');
                WP_CLI::line( '<h2><a href="'.get_permalink( $id).'" target="dailybrief">'.$title.'</a></h2>' );
                WP_CLI::line( 'Published '.$date.' by '.get_the_author() );
                WP_CLI::line( '<p>'.$excerpt.'</p>' );
                WP_CLI::line( '<p>&nbsp;</p>');
            }
        $page++;
        } while ( $query->have_posts() );

        // Output Footer
        if(!empty($options['footer']))
            WP_CLI::line( $options['footer'] );

    }

    }



    // Finally add the command to WP_CLI
    WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
}
