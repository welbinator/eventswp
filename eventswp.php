<?php
/**
 * Plugin Name: EventsWP
 * Description: A modern events management plugin with custom post types, fields, and calendar views.
 * Version: 1.0.1
 * Author: Your Name
 * Text Domain: eventswp
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'EVENTSWP_PLUGIN_DIR' ) ) {
	define( 'EVENTSWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'EVENTSWP_PLUGIN_URL' ) ) {
	define( 'EVENTSWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

define( 'EVENTSWP_VERSION', '1.0.1' );


// Autoloader or manual includes
require_once EVENTSWP_PLUGIN_DIR . 'includes/class-eventswp.php';

// Initialize plugin
function eventswp_init_plugin() {
	$plugin = new \EventsWP\Plugin();
	$plugin->init();
}
add_action( 'plugins_loaded', 'eventswp_init_plugin' );
