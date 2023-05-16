<?php
namespace Relevantly\Matchers;

use Relevantly\Analyzers\KeywordAnalyzer;
use Relevantly\Repositories\KeywordRepository;

class KeywordProximityMatcher implements ContentMatcherInterface {
    private $keywordAnalyzer;
    private $keywordRepository;
	private $results;

	function get_results()
	{
		return $this->results;
	}

	function __construct( KeywordAnalyzer $keywordAnalyzer, KeywordRepository $keywordRepository ) {
		$this->keywordAnalyzer   = $keywordAnalyzer;
		$this->keywordRepository = $keywordRepository;
	}

	function find_related_content( $content_id, $keywords = null, $limit = RELEVANTLY_DEFAULT_LIMIT ) {
		// If keywords are not provided, analyze the content to get the keywords
		if ( empty( $keywords ) ) {
			$post     = get_post( $content_id );
            
			$keywords = $this->keywordAnalyzer->analyze( $post->post_content );
		}

		// Find related content based on the extracted keywords
		$related_posts = $this->keywordRepository->get_related_posts_by_proximity(
			$content_id,
			$keywords,
			$limit
		);

		$this->results = $related_posts;
		
		return $this;
	}
}
