<?php
namespace Relevantly\Repositories;

class TagRepository implements RepositoryInterface {

	function get_related_posts( $content_id, $tags, $limit ) {
		
		if ( ! empty( $tags ) ) {
			$query = new \WP_Query(
				array(
					'tag__and'       => $tags,
					'post__not_in'   => [ $content_id ],
					'posts_per_page' => $limit,
					'fields' => 'ids',
				)
			);

			wp_reset_postdata();
			
			return $query->posts;
		}

		return [];
	}
}
