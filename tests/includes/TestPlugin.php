<?php
namespace Relevantly\Tests\Includes;

class TestPlugin extends \WP_UnitTestCase {
	public function setUp(): void {
		parent::setUp();
		// Initialize the plugin instance
		$this->plugin = \Relevantly\Plugin::get_instance();
	}

	public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        $tables = new \Relevantly\Tables\RelevantlyTables();
        $tables->init();
    }

	public function test_table_creation() {
		$this->assertTrue( $this->table_exists( RELEVANTLY_KEYWORDS_TABLE ) );
	}

	private function table_exists( $table_name ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table_name;
		return $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name;
	}

	public function test_on_create() {
		// Create a post
		$post_id = $this->factory->post->create(
			array(
				'post_title'   => 'Test Post 1',
				'post_content' => 'Created post for testing the keywords.',
			)
		);

		// Get the stored keywords for the created post
		$keywordRepository = new \Relevantly\Repositories\KeywordRepository();
		$stored_keywords = $keywordRepository->get_keywords( $post_id );

		// Assert that the stored keywords match the expected keywords
		// Replace the $expected_keywords array with the actual expected keywords
		$expected_keywords = array( 'created', 'post', 'testing', 'keywords' );
		$this->assertEquals( $expected_keywords, $stored_keywords );
	}

	public function test_on_update() {
		// Create a post
		$post_id = $this->factory->post->create(
			array(
				'post_title'   => 'Test Post 2',
				'post_content' => 'This is a test post.',
			)
		);

		// Update the post
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => 'This is an updated test post.',
			)
		);

		// Get the stored keywords for the updated post
		$keywordRepository = new \Relevantly\Repositories\KeywordRepository();
		$stored_keywords = $keywordRepository->get_keywords( $post_id );

		// Assert that the stored keywords match the expected keywords
		// Replace the $expected_keywords array with the actual expected keywords
		$expected_keywords = array( 'updated', 'test', 'post' );
		$this->assertEquals( $expected_keywords, $stored_keywords );
	}

	public function test_on_trash() {
		// Create a post
		$post_id = $this->factory->post->create(
			array(
				'post_title'   => 'Test Post 2',
				'post_content' => 'This is a test post.',
			)
		);

		// Update the post
		wp_trash_post( $post_id );

		$keywordRepository = new \Relevantly\Repositories\KeywordRepository();
		$stored_keywords = $keywordRepository->get_keywords( $post_id );

		$this->assertEmpty( $stored_keywords );
	}

	public function test_on_delete() {
		// Create a post
		$post_id = $this->factory->post->create(
			array(
				'post_title'   => 'Test Post 2',
				'post_content' => 'This is a test post.',
			)
		);

		// Update the post
		wp_delete_post( $post_id );

		$keywordRepository = new \Relevantly\Repositories\KeywordRepository();
		$stored_keywords = $keywordRepository->get_keywords( $post_id );
		
		$this->assertEmpty( $stored_keywords );
	}
}
