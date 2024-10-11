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
function pdwpst_store_search_query( $s ) {
    global $wpdb;
    static $stored = false;

    if ( '' === $s ) {
        return $s;
    }

    // If the search query has already been stored for this request, return early.
    if ( $stored ) {
        return $s;
    }

    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

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
function pdwpst_get_searches( $query = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

    if ( '' === $query ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
        return $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$table_name}" )
        );
    } else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE search_query LIKE %s",
                '%' . $wpdb->esc_like( $query ) . '%'
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
function pdwpst_get_search_count( $search_query = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

    if ( '' === $search_query ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
    } else {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE search_query LIKE %s",
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
function pdwpst_get_unique_searches() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

    error_log("pdwpst_get_unique_searches: Attempting to query table {$table_name}");

    // Check if the table exists
    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
    error_log("pdwpst_get_unique_searches: Table {$table_name} exists: " . ($table_exists ? 'true' : 'false'));

    if (!$table_exists) {
        error_log("pdwpst_get_unique_searches: Table {$table_name} does not exist!");
        return array();
    }

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
    $results = $wpdb->get_results("SELECT DISTINCT search_query FROM {$table_name}");

    error_log("pdwpst_get_unique_searches: Query executed. Results: " . print_r($results, true));
    error_log("pdwpst_get_unique_searches: Last MySQL error: " . $wpdb->last_error);

    return $results;
}