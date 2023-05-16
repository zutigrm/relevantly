<?php
namespace Relevantly\Analyzers;

class CategoryAnalyzer implements ContentAnalyzerInterface {

	function analyze( $content_id ) {
		$categories = get_the_category( $content_id );

		if ( ! empty( $categories ) )
		{
			return array_map(
				function ( $category ) {
					return $category->term_id;
				},
				$categories
			);
		}

		return [];
	}
}
