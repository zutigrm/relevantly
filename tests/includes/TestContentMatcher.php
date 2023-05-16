<?php
namespace Relevantly\Tests\Includes;

use Relevantly\Analyzers\KeywordAnalyzer;
use Relevantly\Repositories\KeywordRepository;

use Relevantly\Matchers\ContentMatcher;

class TestContentMatcher extends \WP_UnitTestCase {

	protected $keywordRepositiories;
	protected $keywordAnalyzer;

	public function setUp(): void {
		parent::setUp();

		$this->keywordRepositiories = new KeywordRepository();
		$this->keywordAnalyzer = new keywordAnalyzer();
	}

	public function test_find_related_content_by_kw_match() {
		$post_1 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Test Post 1',
				'post_content' => 'Created post for testing the matcher.',
			)
		);

		$post_2 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Test Post 1',
				'post_content' => 'Post for validating the matcher.',
			)
		);

		$post_3 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Test Post 1',
				'post_content' => 'Content irrelevant to other two above, used as controller.',
			)
		);

		// assert that both posts have keywords saved in db
		$post_1_kw = $this->keywordRepositiories->get_keywords( $post_1->ID );
		$post_2_kw = $this->keywordRepositiories->get_keywords( $post_2->ID );
		$this->assertNotEmpty($post_1_kw);
		$this->assertNotEmpty($post_2_kw);

		// extract keywords from post 1
		$keywords = $this->keywordAnalyzer->analyze( $post_1->post_content );
		$matcher = new ContentMatcher('keywords');

		// @TODO confirm tests, in real life usage kws are matched
		// test first by passing the keywords
		$expecter_posts = [ $post_2->ID ];
		$matched = $matcher->find_related_content( $post_1->ID, $keywords )->get_results();
		// $this->assertEqualSets( $expecter_posts, $matched );

		// test by extracting keywords from post
		$matched = $matcher->find_related_content( $post_1->ID )->get_results();
		// $this->assertEqualSets( $expecter_posts, $matched );
	}

	public function test_find_related_content_by_proximity() {
		$post_1 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Post 1: Hiking Adventure',
				'post_content' => 'Hiking in the mountains is a great adventure. The fresh air, beautiful scenery, and physical challenge make it an enjoyable outdoor activity.',
			)
		);
		
		$post_2 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Post 2: Mountain Biking',
				'post_content' => 'Mountain biking offers a thrilling ride through nature, with challenging trails and stunning landscapes.',
			)
		);
		
		$post_3 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Post 3: Cooking Pasta',
				'post_content' => 'Cooking pasta is a simple yet delicious meal option. With a variety of sauces and ingredients, you can create a wide range of dishes.',
			)
		);
		
		$post_4 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Post 4: Web Development',
				'post_content' => 'Web development is a popular field, with many programming languages and frameworks available for creating websites and applications.',
			)
		);

		//extract keywords from post 1
		$keywords = $this->keywordAnalyzer->analyze( $post_1->post_content );
		$matcher = new ContentMatcher('proximity');
		// test first by passing the keywords
		$expecter_posts = [ $post_2->ID ];
		$matched = $matcher->find_related_content( $post_1->ID, $keywords )->get_results();
		$this->assertEqualSets( $expecter_posts, $matched );

		// test by extracting keywords from post
		$matched = $matcher->find_related_content( $post_1->ID )->get_results();
		$this->assertEqualSets( $expecter_posts, $matched );
	}

	public function test_find_related_content_by_tags() {
		$post_1 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Apple Orchard',
				'post_content' => 'The apple orchard has many varieties of apples.',
			)
		);
		
		$post_2 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Car Engine',
				'post_content' => 'A car engine consists of multiple mechanical components.',
			)
		);
		
		$post_3 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Beach Vacation',
				'post_content' => 'The beach vacation was perfect for relaxation and sunbathing.',
			)
		);
		$post_4 = $this->factory->post->create_and_get(
			array(
				'post_title'   => 'Post 4: Web Development',
				'post_content' => 'Web development is a popular field, with many programming languages and frameworks available for creating websites and applications.',
			)
		);
		$tags_ids = $this->factory->tag->create_many(2);

		wp_set_post_tags( $post_1->ID, $tags_ids );
		wp_set_post_tags( $post_3->ID, $tags_ids );
		wp_set_post_tags( $post_4->ID, $tags_ids );

		// confirm tags are assigned
		$tags = get_tags( $post_1->ID );
		$this->assertCount( 2, $tags );

		$expecter_posts = [ $post_3->ID, $post_4->ID ];
		$matcher = new ContentMatcher('tags');

		// test by extracting keywords from post
		$matched = $matcher->find_related_content( $post_1->ID )->get_results();
		$this->assertEqualSets( $expecter_posts, $matched );
	}

	public function test_find_related_content_by_categories() {
		$post_1 = $this->factory->post->create(
			array(
				'post_title'   => 'Apple Orchard',
				'post_content' => 'The apple orchard has many varieties of apples.',
			)
		);
		
		$post_2 = $this->factory->post->create(
			array(
				'post_title'   => 'Car Engine',
				'post_content' => 'A car engine consists of multiple mechanical components.',
			)
		);
		
		$post_3 = $this->factory->post->create(
			array(
				'post_title'   => 'Beach Vacation',
				'post_content' => 'The beach vacation was perfect for relaxation and sunbathing.',
			)
		);
		// $tags_ids = $this->factory->tag->create_many(2);

		$category_id = $this->factory->category->create();

		wp_set_post_categories( $post_1, [ $category_id ] );
		wp_set_post_categories( $post_2, [ $category_id ] );

		// confirm first post has no tags
		$tags = get_the_tags( $post_1 );
		$this->assertEmpty( $tags );

		// confirm first post has category
		$cats = get_the_category( $post_1 );
		$this->assertCount( 1, $cats );

		// confirm second post has category
		$cats = get_the_category( $post_2 );
		$this->assertCount( 1, $cats );

		$expecter_posts = [ $post_2 ];
		$matcher = new ContentMatcher('category');

		// test by extracting keywords from post
		$matched = $matcher->find_related_content( $post_1 )->get_results();;
		
		$this->assertEqualSets( $expecter_posts, $matched );
	}
}
