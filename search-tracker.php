<?php
/**
Plugin Name: Search Tracker
Description: Store and find what your users are searching for on your site
Version: 1.0
Author: PunterDigital
Author URI: https://punterdigital.com
License: GPL v3
text-domain: search-tracker
 *
@package Search_Tracker
 */

use PD_Search_Tracker\Telemetry;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Global variables.
define( 'PDWPST_SEARCH_TRACKER_VERSION', '1.0.0' );
define( 'PDWPST_SEARCH_TRACKER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Telemetry.
require PDWPST_SEARCH_TRACKER_PLUGIN_DIR . 'src/class-telemetry.php';

// Setup and Uninstall.
require PDWPST_SEARCH_TRACKER_PLUGIN_DIR . 'src/setup.php';
require PDWPST_SEARCH_TRACKER_PLUGIN_DIR . 'src/functions.php';

// Pages.
require PDWPST_SEARCH_TRACKER_PLUGIN_DIR . 'src/pages/admin/settings.php';
require PDWPST_SEARCH_TRACKER_PLUGIN_DIR . 'src/pages/admin/searches.php';

// Hooks.
register_activation_hook( __FILE__, 'pdwpst_user_searches_install' );
register_deactivation_hook( __FILE__, 'pdwpst_search_tracker_plugin_deactivate' );

/**
 * Executes when the plugin is deactivated.
 *
 * @return void
 */
function pdwpst_search_tracker_plugin_deactivate() {
	// Deactivate telemetry.
	Telemetry::deactivate();
}

// Filters.
add_filter( 'get_search_query', 'pdwpst_store_search_query' );

Telemetry::init();

/**
 * Enqueues admin scripts
 *
 * @return void
 */
function pdwpst_enqueue_admin_scripts() {
    // Get the URL to the plugin directory
    $plugin_url = plugin_dir_url( __FILE__ );

    // Enqueue the CSS file
    wp_enqueue_style('pdwpst_styles', $plugin_url . 'assets/css/style.css', array(), PDWPST_SEARCH_TRACKER_VERSION);
}
add_action('admin_enqueue_scripts', 'pdwpst_enqueue_admin_scripts');