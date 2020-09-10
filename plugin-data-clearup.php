<?php
/**
 * Plugin Name: Plugin Data ClearUp
 * Plugin URI: https://github.com/EngKhaledB/plugin-data-clearup
 * Description: Adds an action to all uninstallable plugins to deactivate & clear the plugin data without deleting the plugin files.
 * Version: 1.0.0
 * Author: Khaled Abu Alqomboz
 * Author URI: https://github.com/EngKhaledB
 * License: GPLv2 or later
 * Text Domain: plugin-data-clearup
 */

class Plugin_Data_ClearUp {

    private $plugins;

    public function __construct() {
        add_action( 'admin_init', [ $this, 'init' ] );
    }

    function init() {
        $this->plugins = get_plugins();

        if ( current_user_can( 'delete_plugins' ) ) {
            $this->show_message();
            $this->add_clear_button_action();
            $this->process_clear_data();
        }

        $this->clear_data_webhook();
    }

    function add_clear_button_action() {
        foreach ( $this->plugins as $plugin_file => $plugin ) {
            if ( is_uninstallable_plugin( $plugin_file ) ) {
                add_filter( 'plugin_action_links_' . $plugin_file, function ( $links ) use ( $plugin_file, $plugin ) {
                    $links['clear-data'] = sprintf(
                        '<a class="clear-data" style="color: #a00;" onclick="return confirm(\'Are you sure you want to clear %s data?\')" href="%s" target="_parent">%s</a>',
                        $plugin['Name'],
                        wp_nonce_url( 'plugins.php?action=clear-data&plugin=' . urlencode( $plugin_file ), 'clear-plugin-data_' . $plugin_file ),
                        __( 'Clear Data', 'plugin-data-clearup' )
                    );

                    return $links;
                }, 20 );
            }
        }
    }

    function process_clear_data() {
        if (
            isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'clear-data' &&
            strpos( $_SERVER['SCRIPT_NAME'], 'plugins.php' ) !== false &&
            isset( $_REQUEST['plugin'] ) && isset( $_REQUEST['_wpnonce'] ) &&
            wp_verify_nonce( $_REQUEST['_wpnonce'], 'clear-plugin-data_' . $_REQUEST['plugin'] )
        ) {
            $plugin = $_REQUEST['plugin'];
            if ( is_uninstallable_plugin( $plugin ) ) {
                deactivate_plugins( [ $plugin ] );
                uninstall_plugin( $plugin );
                wp_redirect( self_admin_url( "plugins.php?plugin-data-cleared=true" ) );
            }
        }
    }

    function show_message() {
        add_action( 'pre_current_active_plugins', function () {
            if ( isset( $_REQUEST['plugin-data-cleared'] ) ) {
                ?>
                <div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin Deactivated and Data Cleared.' ); ?></p></div>
                <?php
            }
        } );
    }

    function clear_data_webhook() {
        if ( defined( 'PLUGIN_DATA_CLEARUP_WEBHOOK_TOKEN' ) ) {
            add_action( 'wp_ajax_clear_plugin_data_webhook', [ $this, 'clear_plugin_data_webhook_action' ] );
            add_action( 'wp_ajax_nopriv_clear_plugin_data_webhook', [ $this, 'clear_plugin_data_webhook_action' ] );
        }
    }

    function clear_plugin_data_webhook_action() {
        $cleared = 0;

        if ( isset( $_REQUEST['clear-token'] ) && $_REQUEST['clear-token'] === PLUGIN_DATA_CLEARUP_WEBHOOK_TOKEN && isset( $_REQUEST['plugins'] ) ) {
            $request_plugins = explode( ',', $_REQUEST['plugins'] );

            if ( ! empty( $request_plugins ) ) {
                foreach ( $request_plugins as $plugin ) {
                    if ( in_array( $plugin, array_keys( $this->plugins ) ) && is_uninstallable_plugin( $plugin ) ) {
                        deactivate_plugins( [ $plugin ] );
                        uninstall_plugin( $plugin );
                        $cleared ++;
                    }
                }
            }
        }

        wp_die( $cleared ? 1 : 0 );
    }
}

new Plugin_Data_ClearUp();
