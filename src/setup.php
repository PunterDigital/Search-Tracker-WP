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

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Function to install user searches table and initialize plugin.
 */
function pdwpst_user_searches_install() {
    global $wpdb;

    error_log('Starting pdwpst_user_searches_install function');

    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

    error_log("Checking if table {$table_name} exists");

    // Check if the table already exists
    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

    error_log("Table {$table_name} exists: " . ($table_exists ? 'true' : 'false'));

    if (!$table_exists) {
        error_log("Table {$table_name} does not exist. Attempting to create it.");

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            search_query text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        error_log("About to run dbDelta with SQL: " . $sql);

        $result = dbDelta($sql);

        error_log("dbDelta result: " . print_r($result, true));

        // Check if the table was actually created
        $table_created = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

        if ($table_created) {
            error_log("Table {$table_name} created successfully.");
            add_option('pdwpst_user_searches_db_version', USER_SEARCHES_DB_VERSION);
            error_log("Added pdwpst_user_searches_db_version option with value: " . USER_SEARCHES_DB_VERSION);
        } else {
            error_log("Failed to create table {$table_name}. Last MySQL error: " . $wpdb->last_error);
        }
    } else {
        error_log("Table {$table_name} already exists. Checking version.");

        $installed_ver = get_option('pdwpst_user_searches_db_version');

        error_log("Installed version: {$installed_ver}, Current version: " . USER_SEARCHES_DB_VERSION);

        if (USER_SEARCHES_DB_VERSION !== $installed_ver) {
            error_log("Version mismatch. Updating version number.");
            update_option('pdwpst_user_searches_db_version', USER_SEARCHES_DB_VERSION);
            error_log("Updated pdwpst_user_searches_db_version option to: " . USER_SEARCHES_DB_VERSION);
        } else {
            error_log("Version is up to date. No action needed.");
        }
    }

    error_log("Activating Telemetry");
    Telemetry::activate();

    error_log('Finished pdwpst_user_searches_install function');
}