<?php
namespace Relevantly\Tables;

class KeywordsTable implements RelevantlyTableInterface {

	static function create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . RELEVANTLY_KEYWORDS_TABLE;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                post_id bigint(20) UNSIGNED NOT NULL,
                keyword varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY post_id (post_id)
            ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}
	}
}
