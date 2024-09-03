<?php
/**
 * Admin settings page for Search Tracker
 *
 * @package Search_Tracker
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'search_tracker_add_admin_menu' );

/**
 * Add menu item for Search Tracker settings.
 */
function search_tracker_add_admin_menu() {
	add_options_page(
		'Search Tracker Settings',
		'Search Tracker',
		'manage_options',
		'search-tracker-settings',
		'search_tracker_settings_page'
	);
}

/**
 * Display the admin settings page content.
 */
function search_tracker_settings_page() {
	if ( isset( $_POST['submit'] ) && check_admin_referer( 'search_tracker_settings' ) ) {
		$delete_on_uninstall = isset( $_POST['delete_on_uninstall'] ) ? 1 : 0;
		update_option( 'search_tracker_delete_on_uninstall', $delete_on_uninstall );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'search-tracker' ) . '</p></div>';
	}

	$delete_on_uninstall = get_option( 'search_tracker_delete_on_uninstall', 0 );

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Search Tracker Settings', 'search-tracker' ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'search_tracker_settings' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Delete data on uninstall', 'search-tracker' ); ?></th>
					<td>
						<label for="delete_on_uninstall">
							<input type="checkbox" name="delete_on_uninstall" id="delete_on_uninstall" value="1" <?php checked( $delete_on_uninstall, 1 ); ?>>
							<?php esc_html_e( 'Delete all stored search data when the plugin is uninstalled', 'search-tracker' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'search-tracker' ); ?>">
			</p>
		</form>
	</div>
	<?php
}

add_filter( 'plugin_action_links_search-tracker/search-tracker.php', 'search_tracker_add_settings_link' );

/**
 * Add a settings link to the Plugins page.
 *
 * @param array $links Array of plugin action links.
 * @return array Modified array of plugin action links.
 */
function search_tracker_add_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=search-tracker-settings' ) ) . '">' . esc_html__( 'Settings', 'search-tracker' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}