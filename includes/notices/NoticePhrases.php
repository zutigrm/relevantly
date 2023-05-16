<?php
namespace Relevantly\Notices;

class NoticePhrases {

    static function init()
    {
        self::background_extracting_failed();
        self::background_extracting_succeeded();
    }

    static function background_extracting_failed()
    {
        $error = get_option('relevantly_phrases_error');

        if ( ! empty( $error ) ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
            
            delete_option('relevantly_phrases_error'); 
        }
    }

    static function background_extracting_succeeded()
    {
        $error  = get_option('relevantly_phrases_error');
        $done   = get_option('relevantly_phrases_end');
        $notice = get_option('relevantly_phrases_end_notice');

        if ( ! empty( $done ) && empty( $error ) && empty( $notice ) ) {
            $message = __( 'Relevantly Notice - Extracting phrases from existing posts succeeeded.', 'relevantly' );
            
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
            
            update_option('relevantly_phrases_end_notice', true); 
        }
    }
}