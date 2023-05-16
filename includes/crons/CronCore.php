<?php
namespace Relevantly\Crons;

class CronCore {

    static function init()
    {
        self::hooks();
        
        CronPhrases::register_cron();
    }

    static function hooks()
    {
        add_filter( 'cron_schedules', array( __CLASS__, 'custom_schedule_time' ) );
    }

    static function custom_schedule_time( $schedules )
    {
        if( ! isset( $schedules[ "5minutes" ] ) ) {
            $schedules["5minutes"] = array(
                'interval' => 5 * 60,
                'display'  => esc_html__( 'Once every 5 minutes' )
            );
        }

        return $schedules;
    }
} 