<?php
namespace Relevantly\Matchers;

use Relevantly\Analyzers\CategoryAnalyzer;
use Relevantly\Repositories\CategoryRepository;

class CategoryMatcher implements ContentMatcherInterface {
    private $categoryAnalyzer;
    private $categoryRepository;
	private $results;

	function get_results()
	{
		return $this->results;
	}

	function __construct( CategoryAnalyzer $categoryAnalyzer, CategoryRepository $categoryRepository ) {
		$this->categoryAnalyzer   = $categoryAnalyzer;
		$this->categoryRepository = $categoryRepository;
	}

	function find_related_content( $content_id, $categories = null, $limit = RELEVANTLY_DEFAULT_LIMIT ) {
		$content_id = absint( $content_id );

		// If categories are not provided, analyze the content to get the keywords
		if ( empty( $categories ) ) {
			$categories = $this->categoryAnalyzer->analyze( $content_id );
		}
		
		// Find related content based on the extracted keywords
		$related_posts = $this->categoryRepository->get_related_posts(
			$content_id,
			$categories,
			$limit
		);

		/**
		 * Related posts array matched by category results
		 * @param related_posts array resulting array of matched posts ids
		 * @param content_id int current post ID
		 */
		$this->results = apply_filters( 'relevantly_related_posts_by_category', $related_posts, $content_id );
		
		return $this;
	}
}
