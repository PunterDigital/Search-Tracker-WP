<?php
/**
 * Search Tracker functions
 *
 * @package Search_Tracker
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores the search query in the database.
 *
 * @param string $s The search query.
 * @return string The sanitized search query.
 */
function store_search_query( $s ) {
	global $wpdb;
	static $stored = false;

	if ( '' === $s ) {
		return $s;
	}

	// If the search query has already been stored for this request, return early.
	if ( $stored ) {
		return $s;
	}

	$table_name = $wpdb->prefix . 'user_searches';

	$s = sanitize_text_field( $s );

	$wpdb->insert(
		$table_name,
		array(
			'time'         => current_time( 'mysql' ),
			'search_query' => $s,
		),
		array(
			'%s',
			'%s',
		)
	);

	$stored = true;

	return $s;
}

/**
 * Get all searches from the database.
 *
 * @param string $query Optional search query to filter results.
 * @return array An array of search results.
 */
function get_searches( $query = '' ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_searches';

	if ( '' === $query ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
		return $wpdb->get_results(
			$wpdb->prepare( 'SELECT * FROM `%s`', $table_name )
		);
	} else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM `%s` WHERE search_query LIKE %s',
				$table_name,
				'%' . $wpdb->esc( $query ) . '%'
			)
		);
	}
}

/**
 * Get the number of searches.
 *
 * @param string $search_query Optional search query to filter count.
 * @return int The number of searches.
 */
function get_search_count( $search_query = '' ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_searches';

	if ( '' === $search_query ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
		return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM `%s`', $table_name ) );
	} else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				'SELECT COUNT(*) FROM `%s` WHERE search_query LIKE %s',
				$table_name,
				'%' . $wpdb->esc_like( $search_query ) . '%'
			)
		);
	}
}

/**
 * Get all unique searches from the database.
 *
 * @return array An array of unique search queries.
 */
function get_unique_searches() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_searches';

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
	return $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT search_query FROM `%s`', $table_name ) );
}
