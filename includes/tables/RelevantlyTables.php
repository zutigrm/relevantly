<?php
namespace Relevantly\Tables;

class RelevantlyTables {

	function __construct() {
		// add custom tables on activation hook
		register_activation_hook( RELEVANTLY_PLUGIN_FILE_PATH, array( $this, 'init' ) );
	}

	/**
	 * initiate tables creation
	 */
	function init() {
		// create keywords table that will be used for content analyzer
		KeywordsTable::create_table();
	}
}
