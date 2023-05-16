<?php
namespace Relevantly\Tests\Includes;

use Relevantly\Analyzers\TagAnalyzer;

class TestTagAnalyzer extends \WP_UnitTestCase {

	protected $tagAnalyzer;

	public function setUp(): void {
		parent::setUp();
		$this->tagAnalyzer = new TagAnalyzer();
	}

	public function test_analyze() {
		$post_id = $this->factory->post->create();
		$tag_ids = $this->factory->tag->create_many( 3 );

		wp_set_post_tags( $post_id, $tag_ids );

		// tags are created
		$total_tags = get_tags();
		$this->assertCount( 3, $total_tags );

		// tags are assigned to the post
		$post_tags = get_the_tags( $post_id );
		$this->assertCount( 3, $post_tags );

		// confirm instance is set
		$this->assertInstanceOf( \Relevantly\Analyzers\TagAnalyzer::class, $this->tagAnalyzer );

		// comfirm analyzer extracted these tags
		$tags = $this->tagAnalyzer->analyze( $post_id );

		$this->assertCount( 3, $tags );
	}
}
