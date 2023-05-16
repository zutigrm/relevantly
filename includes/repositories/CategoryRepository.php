<?php
namespace Relevantly\Repositories;

class CategoryRepository implements RepositoryInterface {

	function get_related_posts( $content_id, $categories, $limit ) {
		
		if ( ! empty( $categories ) ) {
			$query = new \WP_Query(
				array(
					'category__in'   => $categories,
					'post__not_in'   => [ $content_id ],
					'posts_per_page' => $limit,
					'fields'         => 'ids',
				)
			);
			
			wp_reset_postdata();
			
			return $query->posts;
		}

		return [];
	}
}
