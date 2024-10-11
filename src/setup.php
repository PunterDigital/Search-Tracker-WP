<?php
/**
 * Setup functionality for Search Tracker.
 *
 * @package Search_Tracker
 */

use PD_Search_Tracker\Telemetry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'USER_SEARCHES_DB_VERSION', '1.0' );

/**
 * Function to install user searches table and initialize plugin.
 */
function pdwpst_user_searches_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'user_searches';

	// Check if the table already exists.
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            search_query text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$result = dbDelta( $sql );

		if ( empty( $result ) ) {
			// Log the error, but use a proper logging mechanism in production.
			error_log( "Failed to create table $table_name: " . $wpdb->last_error ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			return;
		}

		add_option( 'user_searches_db_version', USER_SEARCHES_DB_VERSION );
	} else {
		// Table already exists, check if we need to update.
		$installed_ver = get_option( 'user_searches_db_version' );

		if ( USER_SEARCHES_DB_VERSION !== $installed_ver ) {
			// Perform update operations here if needed.
			// For example, you might alter the table structure.

			update_option( 'user_searches_db_version', USER_SEARCHES_DB_VERSION );
		}
	}

	Telemetry::activate();
}
