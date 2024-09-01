<?php
// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check if we should delete all data
$delete_on_uninstall = get_option( 'search_tracker_delete_on_uninstall', 0 );

if ( $delete_on_uninstall ) {
    global $wpdb;

    // Define table name
    $table_name = $wpdb->prefix . 'user_searches';

    // Drop the table
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

    // List of all options to delete
    $options_to_delete = array(
        'user_searches_db_version',
        'search_tracker_delete_on_uninstall',
        'search_tracker_telemetry_allowed',
        'search_tracker_show_telemetry_prompt',
        // Add any other plugin-specific options here
    );

    // Delete all listed options
    foreach ( $options_to_delete as $option ) {
        delete_option( $option );
    }

    // Clear any scheduled hooks
    wp_clear_scheduled_hook( 'search_tracker_telemetry_cron' );
    // Add any other scheduled hooks here

    // If you have any user meta, delete it
    // $wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'search_tracker_%'" );

    // If you have any post meta, delete it
    // $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'search_tracker_%'" );

    // Optionally, clear any transients
    // delete_transient( 'search_tracker_some_transient' );
}

// Regardless of whether we're deleting data, clear the telemetry cron
wp_clear_scheduled_hook( 'search_tracker_telemetry_cron' );