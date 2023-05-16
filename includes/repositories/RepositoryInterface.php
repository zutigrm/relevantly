<?php
namespace Relevantly\Repositories;

interface RepositoryInterface {
    public function get_related_posts( $post_id, $keywords, $limit );
}