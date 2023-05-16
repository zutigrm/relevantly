<?php
namespace Relevantly\Crons;

use Relevantly\Analyzers\KeywordAnalyzer;
use Relevantly\Repositories\KeywordRepository;

/**
 * Class CronPhrases
 * 
 * Run "background" process using cron
 */
class CronPhrases {
    private static $cron_process = 'relevantly_phrases_process';
    private static $cron_extract    = 'relevantly_phrases_extract';

    static function register_cron()
    {
        if ( ! wp_next_scheduled( self::$cron_process ) ) {
            wp_schedule_event( time(), '5minutes', self::$cron_process );
        }

        if ( ! wp_next_scheduled( self::$cron_extract ) ) {
            wp_schedule_event( time(), '5minutes', self::$cron_extract );
        }

        add_action( self::$cron_process, array( __CLASS__, 'start_phrases_process' ) );
        add_action( self::$cron_extract, array( __CLASS__, 'extract_phrases' ) );
    }

    /**
     *  @todo attach this to remove crons after process is done 
     */ 
    static function unregister_cron() {
        wp_clear_scheduled_hook(self::$cron_process);
        wp_clear_scheduled_hook(self::$cron_extract);
    }

    static function start_phrases_process()
    {
        $settings = get_option( 'relevantly' );
        $process_start = get_option( 'relevantly_phrases_start' );
		
		if ( ! empty( $settings ) && empty( $process_start ) ) {

			if ( isset( $settings[ 'relatedEnabled' ] ) && '1' == $settings[ 'relatedEnabled' ] ) {
                $page = 1;

                update_option( 'relevantly_phrases_start', true );
                update_option( 'relevantly_phrases_page', $page );
            }
        }
    }

    static function extract_phrases()
    {
        $start = get_option( 'relevantly_phrases_start' );
        $end   = get_option( 'relevantly_phrases_end' );
        $page  = get_option( 'relevantly_phrases_page' );
        $page = (int)$page;
        // use attepmts within a minute to monitor in case
        // website does not receives traffic for a while and delayed crons start
        // executing consecutively too many times, or the process is too slow
        // to run on current env bellow 30 sec
        $attempts = get_option('relevantly_phrases_attempts', 0);

        if ( ! empty( $start ) && empty( $end ) ) {
            $posts = self::get_posts( $page );

            if ( ! empty( $posts ) ) {
                $start_time = microtime(true);
                
                foreach ( $posts as $post ) {
                    self::extract_from_post( $post );

                    if ( microtime(true) - $start_time > 28 ) {
                        if ( (int)$attempts > 5 ) { // If more than 5 attempts
                            $message = esc_html__( 'Phrase extraction failed to run in the background. You will need to manually update your posts for which you want to start getting phrases matching.', 'relevantly' );
                            update_option('relevantly_phrases_error', $message );
                            // stop the process in this case
                            update_option( 'relevantly_phrases_end', true );
                       
                        } else {
                            update_option('relevantly_phrases_attempts', $attempts); // Update attempts count
                        }
                        break;
                    }
                }
    
                update_option( 'relevantly_phrases_page', $page + 1 );

            } else {
                // if no results found, we are on last page, finish the process
                update_option( 'relevantly_phrases_end', true );
            }
        }
    }

    static function get_posts( $page )
    {
        $posts = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'paged'          => $page,
        ]);

        return $posts;
    }

    static function extract_from_post( \WP_Post $post )
    {
        $analyzers  = new KeywordAnalyzer();
        $repository = new KeywordRepository();

        if ( ! empty( $post->post_content ) ) {
            $keywords = $analyzers->analyze( $post->post_content );

            $repository->delete_keywords( $post->ID );
            $repository->insert_keywords( $post->ID, $keywords );
        }
    }
}