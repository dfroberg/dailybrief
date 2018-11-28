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

    function test( $args, $assoc_args ) {
        WP_CLI::line( '=== Test successful ===' );
        $tomorrow = strtotime("+1 day");
        $tomorrow = date('Y-m-d',$tomorrow);
        $today = date('Y-m-d');
        WP_CLI::line( 'Today: '.$today);
        WP_CLI::line( 'Tomorrow: '. $tomorrow);

    }

    function posts( $args, $assoc_args ) {
        global $wpdb;
        $tomorrow = strtotime("+1 day");
        $tomorrow = date('Y-m-d',$tomorrow);
        $today = date('Y-m-d');
        $before_date = $tomorrow;
        $after_date = $today;
        $exclude_posts = array();
        $failed_posts = array();
        $status = array( 'publish' );
        $types = array( 'post' );

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
    }

    }

    // Finally add the command to WP_CLI
    WP_CLI::add_command( 'dailybrief', 'DailyBrief_CLI_Command' );
}
