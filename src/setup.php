<?php
// Prevent direct file access
use Search_Tracker\Telemetry;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'USER_SEARCHES_DB_VERSION', '1.0' );

/**
 * Function to install user searches
 */
function user_searches_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_searches';

    // Check if the table already exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            search_query text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $result = $wpdb->query( $sql );

        if ( false === $result ) {
            error_log( "Failed to create table $table_name: " . $wpdb->last_error );
            return;
        }

        add_option( 'user_searches_db_version', USER_SEARCHES_DB_VERSION );
    } else {
        // Table already exists, check if we need to update
        $installed_ver = get_option( "user_searches_db_version" );

        if ( $installed_ver != USER_SEARCHES_DB_VERSION ) {
            // Perform update operations here if needed
            // For example, you might alter the table structure

            update_option( "user_searches_db_version", USER_SEARCHES_DB_VERSION );
        }
    }

    Telemetry::activate();
}