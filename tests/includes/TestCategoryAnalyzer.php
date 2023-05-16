<?php
namespace Relevantly\Tests\Includes;

use Relevantly\Analyzers\CategoryAnalyzer;

class TestCategoryAnalyzer extends \WP_UnitTestCase {

	protected $categoryAnalyzer;

	public function setUp(): void {
		parent::setUp();
		$this->categoryAnalyzer = new CategoryAnalyzer();
	}

	public function test_analyze() {
		$post_id = $this->factory->post->create();
		$category_id = $this->factory->category->create();

		wp_set_post_categories( $post_id, [ $category_id ] );

		// tags are created
		$total_cats = get_categories();
		$this->assertCount( 1, $total_cats );

		// tags are assigned to the post
		$post_cats = get_the_category( $post_id );
		$this->assertCount( 1, $post_cats );

		// comfirm analyzer extracted these tags
		$categories = $this->categoryAnalyzer->analyze( $post_id );

		$this->assertCount( 1, $categories );
	}
}
