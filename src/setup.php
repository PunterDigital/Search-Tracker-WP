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

    $table_name = $wpdb->prefix . 'pdwpst_user_searches';

    // Check if the table already exists
    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

    if (!$table_exists) {

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            search_query text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $result = dbDelta($sql);

        // Check if the table was actually created
        $table_created = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

        if ($table_created) {
            add_option('pdwpst_user_searches_db_version', USER_SEARCHES_DB_VERSION);
        }
    } else {

        $installed_ver = get_option('pdwpst_user_searches_db_version');

        if (USER_SEARCHES_DB_VERSION !== $installed_ver) {
            update_option('pdwpst_user_searches_db_version', USER_SEARCHES_DB_VERSION);
        }
    }
    Telemetry::activate();
}