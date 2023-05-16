<?php
/*
Plugin Name: Relevantly
Plugin URI:
Description: This plugin can analyze the content of a user's website and automatically suggest related posts or pages to keep visitors engaged and increase their time on the site.
Author: Aleksej Vukomanovic
Version: 0.8.0
Author URI: https://github.com/zutigrm
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'RELEVANTLY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'RELEVANTLY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RELEVANTLY_PLUGIN_FILE_PATH', __FILE__ );

define( 'RELEVANTLY_VERSION', '0.8.0' );

define( 'RELEVANTLY_DEFAULT_LIMIT', 3 );

if ( ! defined( 'RELEVANTLY_KEYWORDS_TABLE' ) ) {
	define( 'RELEVANTLY_KEYWORDS_TABLE', 'post_keywords' );
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Include the main plugin file
require_once RELEVANTLY_PLUGIN_PATH . 'includes/plugin.php';

// Initialize the plugin
$content_recommendation_plugin = Relevantly\Plugin::get_instance();
