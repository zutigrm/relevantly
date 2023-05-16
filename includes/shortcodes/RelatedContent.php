<?php
namespace Relevantly\Shortcodes;

use Relevantly\Matchers\ContentMatcher;

class RelatedContent {
	public static function init() {
		add_shortcode( 'relevantly', array( __CLASS__, 'render_shortcode' ) );
	}

	public static function render_shortcode( $atts ) {
		$defaults = [
			'limit' => 5,
		];

		$args = shortcode_atts( 
			$defaults, 
			$atts, 
			'relevantly' 
		);

		$current = get_queried_object_id();

		$matcher = new ContentMatcher();

		$related_posts = $matcher->find_related_content( $current, null, $args[ 'limit' ] )->get_results();
		
		ob_start();
		\Relevantly\Utils\Helpers::get_plugin_template_part( 
			'related-posts', 
			null, 
			[ 'related_posts' => $related_posts ] 
		);

		$output = ob_get_clean();

		return $output;
	}
}