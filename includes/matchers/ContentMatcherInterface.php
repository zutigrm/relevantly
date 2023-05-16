<?php
namespace Relevantly\Matchers;

interface ContentMatcherInterface {
	public function find_related_content( $content_id, $keywords, $limit);
}
