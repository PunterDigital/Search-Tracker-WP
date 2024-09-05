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

use Search_Tracker\Telemetry;

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
register_activation_hook( __FILE__, 'user_searches_install' );
register_deactivation_hook( __FILE__, 'search_tracker_plugin_deactivate' );

/**
 * Executes when the plugin is deactivated.
 *
 * @return void
 */
function search_tracker_plugin_deactivate() {
	// Deactivate telemetry.
	Telemetry::deactivate();
}

// Filters.
add_filter( 'get_search_query', 'store_search_query' );

Telemetry::init();
