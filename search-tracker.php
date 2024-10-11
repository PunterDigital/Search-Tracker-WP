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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use PD_Search_Tracker\Telemetry;

// Global variables.
define( 'SEARCH_TRACKER_VERSION', '1.0.0' );
define( 'SEARCH_TRACKER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once SEARCH_TRACKER_PLUGIN_DIR . 'src/class-telemetry.php';

// Telemetry.
require 'src/class-telemetry.php';

// Setup and Uninstall.
require 'src/setup.php';
require 'src/functions.php';

// Pages.
require 'src/pages/admin/settings.php';
require 'src/pages/admin/searches.php';

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
 * @param $hook
 * @return void
 */
function pdwpst_enqueue_admin_scripts($hook) {
    // Enqueue the CSS file
    wp_enqueue_style('pdwpst_styles', get_template_directory_uri() . 'assets/css/style.css');
}
add_action('admin_enqueue_scripts', 'pdwpst_enqueue_admin_scripts');