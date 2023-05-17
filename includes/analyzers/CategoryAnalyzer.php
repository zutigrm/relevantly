<?php
namespace Relevantly\Analyzers;

class CategoryAnalyzer implements ContentAnalyzerInterface {

	function analyze( $content_id ) {
		$categories = get_the_category( $content_id );

		if ( ! empty( $categories ) )
		{
			$results = array_map(
				function ( $category ) {
					return $category->term_id;
				},
				$categories
			);

			/**
			 * Filter for returned results after
			 * extracting categories from the post
			 * @param results array resulting array of category term ids
			 * @param content_id int current post ID
			 */
			return apply_filters( 'relevantly_analyzed_categories_ids', $results, $content_id );
		}

		return [];
	}
}
