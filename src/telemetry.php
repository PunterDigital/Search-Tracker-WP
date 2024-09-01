<?php
namespace Search_Tracker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Search_Tracker\Telemetry')) {
    class Telemetry {
        public static function init() {
            add_action('admin_init', array(__CLASS__, 'check_telemetry_prompt'));
            add_action('admin_post_search_tracker_telemetry_opt_in', array(__CLASS__, 'handle_telemetry_opt_in'));
            add_action('admin_post_search_tracker_telemetry_opt_out', array(__CLASS__, 'handle_telemetry_opt_out'));
            add_action('search_tracker_telemetry_cron', array(__CLASS__, 'send_telemetry_data'));
        }

        public static function activate() {
            add_option('search_tracker_show_telemetry_prompt', true);
            if (!wp_next_scheduled('search_tracker_telemetry_cron')) {
                wp_schedule_event(time(), 'daily', 'search_tracker_telemetry_cron');
            }
        }

        public static function deactivate() {
            wp_clear_scheduled_hook('search_tracker_telemetry_cron');
        }

        public static function check_telemetry_prompt() {
            if (get_option('search_tracker_show_telemetry_prompt', false)) {
                add_action('admin_notices', array(__CLASS__, 'display_telemetry_prompt'));
            }
        }

        public static function display_telemetry_prompt() {
            ?>
            <div class="notice notice-info is-dismissible">
                <p><?php _e('Would you like to help improve Search Tracker by sending anonymous usage data?', 'search-tracker'); ?></p>
                <p><?php _e('We collect your WordPress version, installed plugins (and which ones are active), PHP version, and active theme. No sensitive data is collected.', 'search-tracker'); ?></p>
                <p>
                    <a href="<?php echo admin_url('admin-post.php?action=search_tracker_telemetry_opt_in'); ?>" class="button button-primary"><?php _e('Yes, I\'d like to help', 'search-tracker'); ?></a>
                    <a href="<?php echo admin_url('admin-post.php?action=search_tracker_telemetry_opt_out'); ?>" class="button"><?php _e('No, thanks', 'search-tracker'); ?></a>
                </p>
            </div>
            <?php
        }

        public static function handle_telemetry_opt_in() {
            update_option('search_tracker_telemetry_allowed', true);
            delete_option('search_tracker_show_telemetry_prompt');
            self::send_telemetry_data();
            wp_redirect(admin_url('plugins.php?telemetry=opted-in'));
            exit;
        }

        public static function handle_telemetry_opt_out() {
            update_option('search_tracker_telemetry_allowed', false);
            delete_option('search_tracker_show_telemetry_prompt');
            wp_redirect(admin_url('plugins.php?telemetry=opted-out'));
            exit;
        }

        public static function send_telemetry_data() {
            if (!get_option('search_tracker_telemetry_allowed', false)) {
                return;
            }

            global $wp_version;

            $event_data = array(
                'wordpress_version' => $wp_version,
                'php_version' => phpversion(),
                'active_theme' => wp_get_theme()->get('Name'),
                'plugins' => array()
            );

            $all_plugins = get_plugins();

            foreach($all_plugins as $plugin_path => $plugin_data) {
                $event_data['plugins'][] = array(
                    'name' => $plugin_data['Name'],
                    'version' => $plugin_data['Version'],
                    'is_active' => is_plugin_active($plugin_path)
                );
            }

            $data = array(
                'name' => 'Search Tracker',
                'version' => SEARCH_TRACKER_VERSION,
                'event_type' => 'plugin_activated',
                'event_data' => $event_data
            );

            $response = wp_remote_post('https://telemetry.punterdigital.com/api/collect', array(
                'body' => json_encode($data),
                'headers' => array('Content-Type' => 'application/json'),
                'timeout' => 15
            ));

            if (is_wp_error($response)) {
                error_log('Search Tracker: Telemetry data sending failed: ' . $response->get_error_message());
            }
        }
    }
}