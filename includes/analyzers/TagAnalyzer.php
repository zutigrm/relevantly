<?php
namespace Relevantly\Analyzers;

class TagAnalyzer implements ContentAnalyzerInterface {
	public function analyze( $content_id ) {
		$tags    = get_the_tags( $content_id );
		$tag_ids = [];

		if ( ! empty( $tags ) ) {
			foreach ( $tags as $tag ) {
				$tag_ids[] = $tag->term_id;
			}
		}
		
		/**
		 * Filter the results after tags are
		 * extracted from the post
		 * @param $tag_ids array resulting array of category term ids
		 * @param $content_id int current post ID
		 */
		return apply_filters( 'relevantly_analyzed_tag_ids', $tag_ids, $content_id );
	}
}
