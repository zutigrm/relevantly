<?php
namespace Relevantly\Dashboard;

class DashboardPage {
	
	static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'create_page' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_dashboard_scripts' ] );
	}

	static function create_page() {
		add_menu_page(
			'Relevantly Dashboard',
			'Relevantly',
			'manage_options',
			'relevantly-dashboard',
			[ __CLASS__, 'relevantly_render_dashboard_page' ],
			'',
			3
		);
	}

	static function relevantly_render_dashboard_page() {
		echo '<div id="relevantly-dashboard"></div>';

		do_action( 'relevantly_after_dashboard' );
	}

	static function enqueue_dashboard_scripts( $hook_suffix ) {
		if ('toplevel_page_relevantly-dashboard' !== $hook_suffix) {
			return;
		}
		
		if ( is_admin() ) {
			wp_enqueue_script(
				'relevantly-dashboard',
				plugins_url( 'dashboard/build/index.js', RELEVANTLY_PLUGIN_FILE_PATH ),
				array( 'wp-element', 'wp-api-fetch' ),
				filemtime( RELEVANTLY_PLUGIN_PATH . 'dashboard/build/index.js' ),
				true
			);

			wp_enqueue_style(
				'relevantly-dashboard',
				plugins_url( 'dashboard/build/style-index.css', RELEVANTLY_PLUGIN_FILE_PATH ),
				[],
				filemtime( RELEVANTLY_PLUGIN_PATH . 'dashboard/build/index.js' ),
			);
		}
	}	
}