<?php
namespace Relevantly\Caching;

class CacheManager {
	private $cache_key_prefix = 'relevantly_';

	/**
	 * Get cached data by key.
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	public function get( $key ) {
		$key   = $this->cache_key_prefix . $key;
		$value = get_transient( $key );

		return $value !== false ? $value : null;
	}

	/**
	 * Set cache data with an expiration.
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $expiration
	 * @return bool
	 */
	public function set( $key, $value, $expiration ) {
		$key = $this->cache_key_prefix . $key;

		return set_transient( $key, $value, $expiration );
	}

	/**
	 * Delete cache data by key.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete( $key ) {
		$key = $this->cache_key_prefix . $key;

		return delete_transient( $key );
	}
}
