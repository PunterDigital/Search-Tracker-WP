<?php
/**
 * Uninstall Search Tracker
 *
 * @package Search_Tracker
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if we should delete all data.
$delete_on_uninstall = get_option( 'pdwpst_search_tracker_delete_on_uninstall', 0 );

if ( $delete_on_uninstall ) {
	global $wpdb;

	// Define table name.
	$table_name = $wpdb->prefix . 'pdwpst_user_searches';

	// Drop the table.
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $table_name ) );

	// List of all options to delete.
	$options_to_delete = array(
		'pdwpst_user_searches_db_version',
		'pdwpst_search_tracker_delete_on_uninstall',
		'pdwpst_search_tracker_telemetry_allowed',
		'pdwpst_search_tracker_show_telemetry_prompt',
	);

	// Delete all listed options.
	foreach ( $options_to_delete as $option ) {
		delete_option( $option );
	}

	// Clear any scheduled hooks.
	wp_clear_scheduled_hook( 'pdwpst_search_tracker_telemetry_cron' );
}

// Regardless of whether we're deleting data, clear the telemetry cron.
wp_clear_scheduled_hook( 'pdwpst_search_tracker_telemetry_cron' );
