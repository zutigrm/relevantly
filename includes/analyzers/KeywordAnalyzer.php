<?php
namespace Relevantly\Analyzers;

use DonatelloZa\RakePlus\RakePlus;

class KeywordAnalyzer implements ContentAnalyzerInterface {

	private $rake;

	public function __construct() {
		$this->rake = new RakePlus();
	}

	public function analyze( $content ) {
		$content  = $this->clean_gutenberg_content( $content );
		/**
		 * Filter the content before
		 * it goes for keywords proximity analyzer for extraction
		 * @param content string cleaned post content
		 */
		$content  = apply_filters( 'relevantly_process_kw_content', $content );

		$keywords = $this->rake->extract( $content )->keywords();

		/**
		 * Filter for returned keywords after rake lib
		 * processed and extracted them
		 * @param keywords array array of extracted phrases/keywords
		 */
		return apply_filters( 'relevantly_analyzed_keywords', $keywords );
	}

	function clean_gutenberg_content( $content ) {
		// Remove Gutenberg block comments
		$content = preg_replace( '/<!--\s*\/?wp:.*?-->/s', '', $content );

		// Remove HTML tags
		$content = preg_replace( '/<[^>]+>/s', '', $content );
	
		// Remove shortcodes
		$content = preg_replace( '/\[(\[?)(.*?)(?(1)\]|])\]/s', '', $content );
	   
		return $content;
	}
}
