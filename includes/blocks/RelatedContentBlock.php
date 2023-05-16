<?php
namespace Relevantly\Blocks;

use Relevantly\Matchers\ContentMatcher;

class RelatedContentBlock {
	public static function init() {
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	public static function enqueue_block_editor_assets() 
	{
		wp_enqueue_script(
			'relevantly-related-content-block',
			plugins_url( 'assets/js/index.js', RELEVANTLY_PLUGIN_FILE_PATH ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( RELEVANTLY_PLUGIN_PATH . 'assets/js/index.js' ),
			true
		);
	}

	static function register_block()
	{
		if ( ! function_exists( 'register_block_type' ) ) {
			// Block editor is not available.
			return;
		}

		register_block_type( 'relevantly/related-content', array(
			'editor_script'   => 'relevantly-related-content-block',
			'render_callback' => [ __CLASS__, 'render_related_content_block' ],
		) );
	}

	static function render_related_content_block( $attributes, $content ) {
		$limit         = isset( $attributes['numberOfRecommendations'] ) ? esc_html( $attributes['numberOfRecommendations'] ) : 2;
		$section_title = isset( $attributes['sectionTitle'] ) ? esc_attr( $attributes['sectionTitle'] ) : '';

		$content_id = get_queried_object_id();
	
		$matcher = new ContentMatcher();
		$related_posts = $matcher->find_related_content( $content_id, null, $limit )->get_results();
	
		// Start output buffering
		ob_start();
	
		\Relevantly\Utils\Helpers::get_plugin_template_part( 'recommended-block', null, [ 
			'related_posts' => $related_posts,
			'section_title' => $section_title,
		] );
	
		$output = ob_get_clean();
	
		return $output;
	}	
}
