<?php
namespace Relevantly\Matchers;

use Relevantly\Analyzers\TagAnalyzer;
use Relevantly\Repositories\TagRepository;

class TagMatcher implements ContentMatcherInterface {
    private $tagAnalyzer;
    private $tagRepository;
	private $results = [];

	function get_results()
	{
		return $this->results;
	}

	function __construct( TagAnalyzer $tagAnalyzer, TagRepository $tagRepository ) {
		$this->tagAnalyzer   = $tagAnalyzer;
		$this->tagRepository = $tagRepository;
	}

	function find_related_content( $content_id, $tags = null, $limit = RELEVANTLY_DEFAULT_LIMIT ) {
		// If tags are not provided, analyze the content to get the keywords
		if ( empty( $tags ) ) {
			$tags = $this->tagAnalyzer->analyze( $content_id );
		}

		// Find related content based on the extracted tags
		if ( ! empty( $tags ) ) {
			$related_posts = $this->tagRepository->get_related_posts(
				$content_id,
				$tags,
				$limit
			);
	
			/**
			 * Related posts array matched by tags
			 * @param related_posts array resulting array of matched posts ids
			 * @param content_id int current post ID
			 */
			$this->results = apply_filters( 'relevantly_related_posts_by_tags', $related_posts, $content_id );
		}
		
		return $this;
	}
}
