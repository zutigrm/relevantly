<?php
namespace Relevantly\Tests\Includes;

use Relevantly\Analyzers\KeywordAnalyzer;

class TestKeywordAnalyzer extends \WP_UnitTestCase {

	protected $keywordAnalyzer;

	public function setUp(): void {
		parent::setUp();

		// (new TestPlugin())->test_table_creation();
		$this->keywordAnalyzer = new KeywordAnalyzer();
	}

	public function test_analyze() {
		$content           = 'The quick brown fox jumps over the lazy dog.';
		$expected_keywords = array( 'quick', 'brown', 'fox', 'jumps', 'lazy', 'dog' );

		$keywords = $this->keywordAnalyzer->analyze( $content );

		$this->assertEquals( $expected_keywords, $keywords );
	}
}
