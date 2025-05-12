<?php
namespace EventsWP;

defined( 'ABSPATH' ) || exit;

use EventsWP\Meta;
require_once EVENTSWP_PLUGIN_DIR . 'includes/register-meta.php';
use EventsWP\Settings;
require_once EVENTSWP_PLUGIN_DIR . 'includes/class-settings.php';


class Plugin {

	public function init() {
        add_action( 'init', [ $this, 'register_post_types' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ] );
        add_action( 'init', [ $this, 'register_blocks' ] );

        Meta::init();
        Settings::init();
    
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );

        add_filter( 'single_template', [ $this, 'load_custom_single_template' ] );
    }

    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'eventswp-editor-sidebar',
            EVENTSWP_PLUGIN_URL . 'assets/js/editor-sidebar.js',
            [ 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' ],
            EVENTSWP_VERSION,
            true
        );
    }
    
    public function enqueue_frontend_styles() {
        wp_enqueue_style(
            'eventswp-frontend',
            EVENTSWP_PLUGIN_URL . 'assets/css/style.css',
            [],
            EVENTSWP_VERSION
        );
    }
    
   

    public function register_blocks() {
        // Register editor script handle manually
        wp_register_script(
            'eventswp-events-block',
            EVENTSWP_PLUGIN_URL . 'src/events-block/index.js',
            [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
            EVENTSWP_VERSION,
            true
        );
    
        // Register the block
        register_block_type(
            EVENTSWP_PLUGIN_DIR . 'build/events-block',
            [
                'render_callback' => 'eventswp_render_events_block'
            ]
        );
    
        // Include render file
        $events_block_render = EVENTSWP_PLUGIN_DIR . 'build/events-block/render.php';
        if ( file_exists( $events_block_render ) ) {
            include_once $events_block_render;
        }
    }
    
    
    

	public function register_post_types() {
        register_post_type( 'eventswp-event', [
            'labels' => [
                'name'                  => __( 'Events', 'eventswp' ),
                'singular_name'         => __( 'Event', 'eventswp' ),
                'add_new'               => __( 'Add New', 'eventswp' ),
                'add_new_item'          => __( 'Add New Event', 'eventswp' ),
                'edit_item'             => __( 'Edit Event', 'eventswp' ),
                'new_item'              => __( 'New Event', 'eventswp' ),
                'view_item'             => __( 'View Event', 'eventswp' ),
                'view_items'            => __( 'View Events', 'eventswp' ),
                'search_items'          => __( 'Search Events', 'eventswp' ),
                'not_found'             => __( 'No events found', 'eventswp' ),
                'not_found_in_trash'    => __( 'No events found in Trash', 'eventswp' ),
                'all_items'             => __( 'All Events', 'eventswp' ),
                'archives'              => __( 'Event Archives', 'eventswp' ),
                'insert_into_item'      => __( 'Insert into event', 'eventswp' ),
                'uploaded_to_this_item' => __( 'Uploaded to this event', 'eventswp' ),
                'filter_items_list'     => __( 'Filter events list', 'eventswp' ),
                'items_list'            => __( 'Events list', 'eventswp' ),
                'items_list_navigation' => __( 'Events list navigation', 'eventswp' ),
                'menu_name'             => __( 'Events', 'eventswp' ),
                'name_admin_bar'        => __( 'Event', 'eventswp' ), // <- this is the one that controls the admin bar text like "Add Event"
            ],
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'supports'           => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
        ] );
    }
    

	public function register_taxonomies() {
		register_taxonomy( 'event-category', 'eventswp-event', [
			'label'        => __( 'Event Categories', 'eventswp' ),
			'hierarchical' => true,
			'show_in_rest' => true,
			'public'       => true,
		] );

		register_taxonomy( 'event-type', 'eventswp-event', [
			'label'        => __( 'Event Types', 'eventswp' ),
			'hierarchical' => false,
			'show_in_rest' => true,
			'public'       => true,
		] );
	}

    public function load_custom_single_template( $template ) {
        if ( is_singular( 'eventswp-event' ) ) {
            $custom = EVENTSWP_PLUGIN_DIR . 'templates/single-eventswp-event.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }
        return $template;
    }
    
}
