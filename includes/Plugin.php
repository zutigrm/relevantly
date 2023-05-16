<?php
namespace Relevantly;

use Relevantly\Analyzers\KeywordAnalyzer;
use Relevantly\Shortcodes\RelatedContent as RelatedContentShortcode;
use Relevantly\Notices\NoticeCore;
use Relevantly\Crons\CronCore;
use Relevantly\Blocks\BlockCore;

// tables
use Relevantly\Tables\RelevantlyTables;

// repositories
use Relevantly\Repositories\KeywordRepository;

// dashboard
use Relevantly\Dashboard\DashboardPage;

// rest api classes
use Relevantly\Rest\RestCore;
use Relevantly\Rest\RestSettings;
use Relevantly\Rest\RestPhrases;

class Plugin {
	private static $instance = null;

	private $keywordAnalyzer;
	private $keywordRepository;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() 
	{
		$this->init();
		$this->rest_init();
	}

	function hooks() 
	{
		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'save_post', [ $this, 'on_update' ], 10, 3 );
		add_action( 'trashed_post', [ $this, 'on_delete' ] );
		add_action( 'deleted_post', [ $this, 'on_delete' ] );
		
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );

		add_action( 'widgets_init', [ $this, 'register_widgets' ] );

		add_filter( 'the_content', [ $this, 'after_post' ], 50 );
	}

	function init() 
	{
        // Setup custom tables
        new RelevantlyTables();
		
		$this->keywordRepository = new KeywordRepository();
		$this->keywordAnalyzer   = new KeywordAnalyzer();

		// Initialize and register the dashboard page
		DashboardPage::init();
		RelatedContentShortcode::init();
		BlockCore::init();

		new NoticeCore();

		// init hooks
		$this->hooks();
	}

	function rest_init()
	{
		$restSettings = new RestSettings();
		$restPhrases  = new RestPhrases( $this->keywordRepository );

		$restCore = new RestCore( $restSettings, $restPhrases );
		$restCore->init();
	}

	function load_assets()
	{
		wp_enqueue_style( 'relevantly-main-css', RELEVANTLY_PLUGIN_URL . 'assets/css/main.css', null, RELEVANTLY_VERSION );
	}

	function on_init()
	{
		CronCore::init();
	}

	function after_post( $content )
	{
		$settings = get_option( 'relevantly' );
		
		if ( ! empty( $settings ) && is_singular( 'post' ) ) {
			
			if ( isset( $settings[ 'relatedEnabled' ] ) && '1' == $settings[ 'relatedEnabled' ] ) {
				$limit = isset( $settings[ 'limit' ] ) ? (int)$settings[ 'limit' ] : 3;
				
				$content .= do_shortcode( '[relevantly limit="' . esc_attr( $limit ) . '"]' );
			}
		}

		return $content;
	}

	function on_update( $post_id, $post, $update ) 
	{
		// Check if the post is published or updated
		if ( $post->post_status === 'publish' || $update ) {
			// Extract keywords from the post content
			$keywords = $this->keywordAnalyzer->analyze( $post->post_content );

			// Delete existing keywords for the post
			$this->keywordRepository->delete_keywords( $post_id );

			// Insert new keywords into the custom table
			$this->keywordRepository->insert_keywords( $post_id, $keywords );
		}
	}

	function on_delete( $post_id ) 
	{
		$this->keywordRepository->delete_keywords( $post_id );
	}

	function register_widgets()
	{
		register_widget( 'Relevantly\\Widgets\\RelatedContentWidget' );
	}
}
