<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Stores the search query in the database
 * @param $s search
 * @return search
 */
function store_search_query($s) {
    global $wpdb;
    static $stored = false;

    if ($s == '') {
        return $s;
    }

    // If the search query has already been stored for this request, return early
    if ( $stored ) {
        return $s;
    }

    $table_name = $wpdb->prefix . 'user_searches';

    $s = sanitize_text_field( $s );

    $wpdb->insert(
        $table_name,
        array(
            'time' => current_time( 'mysql' ),
            'search_query' => $s
        ),
        array(
            '%s',
            '%s'
        )
    );

    $stored = true;

    return $s;
}

/**
 * Get all searches from the database
 * @return array
 */
function get_searches($query = '') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_searches';

    $sql = "SELECT * FROM $table_name";

    if ( $query != '' ) {
        $sql .= " WHERE search_query LIKE %s";
        $sql = $wpdb->prepare( $sql, '%' . $wpdb->esc_like( $query ) . '%' );
    }

    $results = $wpdb->get_results( $sql );

    return $results;
}

/**
 * Get the number of searches
 * @return int
 */
function get_search_count($search_query = '') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_searches';

    $sql = "SELECT COUNT(*) FROM $table_name";

    if ( $search_query != '' ) {
        $sql .= " WHERE search_query LIKE %s";
        $sql = $wpdb->prepare( $sql, '%' . $wpdb->esc_like( $search_query ) . '%' );
    }

    $count = $wpdb->get_var( $sql );

    return $count;
}

/**
 * Get all unique searches from the database
 * @return array
 */
function get_unique_searches()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_searches';

    $sql = "SELECT DISTINCT search_query FROM $table_name";

    $results = $wpdb->get_results($sql);

    return $results;
}