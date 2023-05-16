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
		
		return $tag_ids;
	}
}
