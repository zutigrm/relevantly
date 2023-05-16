<?php
namespace Relevantly\Matchers;

use Relevantly\Analyzers\KeywordAnalyzer;
use Relevantly\Analyzers\CategoryAnalyzer;
use Relevantly\Analyzers\TagAnalyzer;
use Relevantly\Repositories\KeywordRepository;
use Relevantly\Repositories\CategoryRepository;
use Relevantly\Repositories\TagRepository;

class ContentMatcher implements ContentMatcherInterface {
	private $strategy;
	private $results;
	private $combined = false;

	function get_results()
	{
		return $this->results;
	}

	function __construct( $strategy = 'combined' ) {
		$this->strategy = $strategy;
	}

	function get_matcher_strategy()
	{
		$strategy = $this->strategy;

		switch ( $strategy ) {
			case 'keywords':
				$keywordAnalyzer   = new KeywordAnalyzer();
				$keywordRepository = new KeywordRepository();

				return new KeywordMatcher( $keywordAnalyzer, $keywordRepository );
				break;
			case 'proximity':
				$keywordAnalyzer   = new KeywordAnalyzer();
				$keywordRepository = new KeywordRepository();

				return new KeywordProximityMatcher( $keywordAnalyzer, $keywordRepository );
				break;
			case 'tags':
				$tagAnalyzer   = new TagAnalyzer();
				$tagRepository = new TagRepository();

				return new TagMatcher( $tagAnalyzer, $tagRepository );
				break;
			case 'category':
				$categoryAnalyzer   = new CategoryAnalyzer();
				$categoryRepository = new CategoryRepository();

				return new CategoryMatcher( $categoryAnalyzer, $categoryRepository );
				break;
			case 'combined':
				$this->combined = true;
				break;

			default:
				$categoryAnalyzer   = new CategoryAnalyzer();
				$categoryRepository = new CategoryRepository();

				return new CategoryMatcher( $categoryAnalyzer, $categoryRepository );
				break;
		}
	}

	function find_related_content( $content_id, $keywords = null, $limit = RELEVANTLY_DEFAULT_LIMIT ) {
		$matcher = $this->get_matcher_strategy();

		// if we choose combined strategy, do the chain
		if ( $this->combined ) {
			$this->combined_strategies( $content_id, $keywords, $limit );

			return $this;
		}

		// use selectewd strategy
		$related_posts = $matcher->find_related_content( $content_id, $keywords, $limit )->get_results();
		
		$this->results = $related_posts;

		return $this;
	}

	function combined_strategies( $content_id, $keywords, $limit )
	{
		$this->strategy = 'keywords';
		$related_posts = $this->get_matcher_strategy()->find_related_content( $content_id, $keywords, $limit )->get_results();

		if ( empty( $related_posts ) ) {
			$strategies = [ 
				'proximity' ,
				'tags',
				'category',
			];

			$last = count( $strategies );
			$index = 0;

			/**
			 * Walk through strategies and look for match
			 * When posts are found, or we run out of strategies, kill the loop
			 */
			while ( empty( $related_posts ) && $index !== $last ) 
			{
				if ( isset( $strategies[ $index ] ) ) {
					$this->strategy = $strategies[ $index ];
					$related_posts = $this->get_matcher_strategy()->find_related_content( $content_id, $keywords, $limit )->get_results();

					$index++;

				} else {
					// kill the loop
					$index = $last;
					break;
				}
			}

			// check if we have enought posts from the matched strategy
			$match_count = count( $related_posts );
			if ( $match_count < $limit ) {
				$this->strategy = 'category';
				$additional_posts = $this->get_matcher_strategy()->find_related_content( $content_id, $keywords, $limit )->get_results();

				// add more posts to the amount of limit, and then append after
				// the closest matches. This is mostly targeting keyword and proximity matches
				$related_posts = array_merge( $related_posts, $additional_posts );
				$related_posts = array_unique( $related_posts );
			}
		}

		$this->results = $related_posts;
	}
}
