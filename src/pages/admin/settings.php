<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add menu item
add_action( 'admin_menu', 'search_tracker_add_admin_menu' );

function search_tracker_add_admin_menu() {
    add_options_page(
        'Search Tracker Settings',
        'Search Tracker',
        'manage_options',
        'search-tracker-settings',
        'search_tracker_settings_page'
    );
}

// Admin page content
function search_tracker_settings_page() {
    if ( isset( $_POST['submit'] ) && check_admin_referer( 'search_tracker_settings' ) ) {
        $delete_on_uninstall = isset( $_POST['delete_on_uninstall'] ) ? 1 : 0;
        update_option( 'search_tracker_delete_on_uninstall', $delete_on_uninstall );
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    $delete_on_uninstall = get_option( 'search_tracker_delete_on_uninstall', 0 );

    ?>
    <div class="wrap">
        <h1>Search Tracker Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field( 'search_tracker_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Delete data on uninstall</th>
                    <td>
                        <label for="delete_on_uninstall">
                            <input type="checkbox" name="delete_on_uninstall" id="delete_on_uninstall" value="1" <?php checked( $delete_on_uninstall, 1 ); ?>>
                            Delete all stored search data when the plugin is uninstalled
                        </label>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

// Add a settings link on the Plugins page
add_filter( 'plugin_action_links_search-tracker/search-tracker.php', 'search_tracker_add_settings_link' );

function search_tracker_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=search-tracker-settings">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}