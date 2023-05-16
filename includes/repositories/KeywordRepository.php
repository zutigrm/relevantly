<?php
namespace Relevantly\Repositories;

class KeywordRepository {
	private $wpdb;
	private $table_name;

	function __construct() {
		global $wpdb;

		$this->wpdb       = $wpdb;
		$this->table_name = $wpdb->prefix . RELEVANTLY_KEYWORDS_TABLE;
	}

	function insert_keywords( $post_id, $keywords ) {
		$table_name = $this->table_name;

		// Define the columns to insert into
		$columns = array(
			'post_id',
			'keyword',
		);

		// Initialize arrays to hold the values and placeholders for the prepared statement
		$values       = array();
		$placeholders = array();

		// Loop through the results and add each set of values and placeholders to the arrays
		foreach ( $keywords as $keyword ) {
			$values[] = array(
				$post_id,
				$keyword,
			);

			$placeholders[] = '( %d, %s )';
		}

		// Build the query with the placeholders
		$query = "INSERT INTO {$table_name} (" . implode( ',', $columns ) . ') VALUES ' . implode( ',', $placeholders );

		// Flatten the values array into a single-dimensional array for the prepared statement
		$flat_values = array();
		foreach ( $values as $row ) {
			$flat_values = array_merge( $flat_values, $row );
		}

		// Prepare the statement and execute it with the flattened values array
		$prepared_query = $this->wpdb->prepare( $query, $flat_values );

		return $this->wpdb->query( $prepared_query );
	}

	function delete_keywords( $post_id ) {
		$table_name = $this->table_name;

		$query = "DELETE FROM $table_name WHERE post_id=%d";

		$this->wpdb->query(
			$this->wpdb->prepare( $query, $post_id )
		);
	}

	function get_related_posts( $post_id, $keywords, $limit = RELEVANTLY_DEFAULT_LIMIT ) {
		$table_name = $this->table_name;

		$keywords_str = implode(
			',',
			array_map(
				function ( $keyword ) {
					return "'" . esc_sql( $keyword ) . "'";
				},
				$keywords
			)
		);

		$sql = "SELECT post_id, COUNT(keyword) as common_keywords
                FROM {$table_name}
                WHERE keyword IN (%s) AND post_id != %d
                GROUP BY post_id
                ORDER BY common_keywords DESC, post_id ASC
                LIMIT %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				$sql,
				$keywords_str,
				$post_id,
				$limit
			)
		);

		return array_map(
			function ( $result ) {
				return $result->post_id;
			},
			$results
		);
	}

	function get_keywords( $post_id, $limit = 999999 ) {
		$table_name = $this->table_name;

		$sql = "SELECT keyword
                FROM {$table_name}
                WHERE post_id = %d
                LIMIT %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				$sql,
				$post_id,
				$limit,
			)
		);

		return array_map(
			function ( $result ) {
				return $result->keyword;
			},
			$results
		);
	}

	function get_all_keywords( $paged = 1, $limit = 999999 ) {
		$table_name = $this->table_name;

		$total = $this->wpdb->get_col( "SELECT COUNT(*) as total FROM {$table_name}" );

		$sql = "SELECT *
                FROM {$table_name}
                LIMIT %d, %d";

		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				$sql,
				$paged === 1 ? 0 : ( $paged - 1 ) * $limit,
				$paged === 1 ? $limit : $limit * $paged,
			)
		);

		$response = [
			'total' => (int)$total[0],
			'items' => $results,
			'next_page' => sprintf( '/relevantly/v1/phrases?page=%d&limit=%d', $paged + 1, $limit ),
		];

		if ( $paged > 1 ) {
			$response[ 'prev_page' ] = sprintf( '/relevantly/v1/phrases?page=%d&limit=%d', $paged - 1, $limit );
		}

		return (object)$response;
	}

	function get_related_posts_by_proximity( $post_id, $keywords, $limit = RELEVANTLY_DEFAULT_LIMIT, $threshold = 1 ) {
		$table_name = $this->table_name;

		$related_posts = array();
		foreach ( $keywords as $keyword ) {
			$sql = "SELECT post_id, keyword
                    FROM {$table_name}
                    WHERE post_id != %d";

			$results = $this->wpdb->get_results(
				$this->wpdb->prepare( $sql, $post_id )
			);

			foreach ( $results as $result ) {
				$distance = levenshtein( strtolower( $result->keyword ), strtolower( $keyword ) );

				if ( $distance <= $threshold ) {

					if ( ! isset( $related_posts[ $result->post_id ] ) ) {
						$related_posts[ $result->post_id ] = 0;
					}

					$related_posts[ $result->post_id ]++;
				}
			}
		}

		arsort( $related_posts );

		return array_slice( array_keys( $related_posts ), 0, $limit, true );
	}

	function clean_table() {
		$table_name = $this->table_name;

		$this->wpdb->query( "DELETE FROM $table_name" );
	}
}
