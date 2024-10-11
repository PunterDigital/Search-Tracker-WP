<?php
/**
 * Displays the admin page to find searches
 *
 * @package Search_Tracker
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Function to display user searches admin page
 */
function pdwpst_user_searches_admin_page() {
	$results = array();

	$unique_searches = pdwpst_get_unique_searches();
	$total_searches  = pdwpst_get_search_count();

	foreach ( $unique_searches as $search ) {
		$results[] = array(
			'search_query' => $search->search_query,
			'count'        => pdwpst_get_search_count( $search->search_query ),
		);
	}

	echo '<div class="wrap">';
		echo '<h1>User Searches</h1>';

	if ( empty( $results ) ) {
		echo '<p>No user searches found.</p>';
		return;
	}

		echo '<table>';
			echo '<tr><th>Search Query</th><th>Count</th><th>Percentage</th><th>Bar</th></tr>';

	foreach ( $results as $row ) {
		$percentage = round( ( $row['count'] / $total_searches ) * 100, 2 );
		echo '<tr><td>' . esc_html( $row['search_query'] ) . '</td><td>' . esc_html( $row['count'] ) . '</td><td>' . esc_html( $percentage ) . '%</td>';
		echo '<td><div class="percentage-bar" style="width:' . esc_html( $percentage ) . '%;"></div></td></tr>';
	}

			echo '</table>';
		echo '</div>';
}

/**
 * Function to add user searches to admin menu
 */
function pdwpst_user_searches_admin_menu() {
	add_menu_page( 'User Searches', 'User Searches', 'manage_options', 'user-searches', 'pdwpst_user_searches_admin_page' );
}

add_action( 'admin_menu', 'pdwpst_user_searches_admin_menu' );
