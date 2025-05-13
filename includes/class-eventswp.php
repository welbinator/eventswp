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
		add_filter( 'the_content', [ $this, 'maybe_override_calendar_page' ] );

		add_action( 'rest_api_init', [ $this, 'register_calendar_endpoint' ] );
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

		$calendar_page_id = get_option( 'eventswp_calendar_page_id' );
		if ( is_page( $calendar_page_id ) ) {
			wp_enqueue_style(
				'fullcalendar-css',
				'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css',
				[],
				'6.1.11'
			);
			wp_enqueue_script(
				'fullcalendar-js',
				'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js',
				[],
				'6.1.11',
				true
			);
			wp_enqueue_script(
				'eventswp-calendar-js',
				EVENTSWP_PLUGIN_URL . 'assets/js/calendar.js',
				[ 'fullcalendar-js' ],
				EVENTSWP_VERSION,
				true
			);

			wp_localize_script( 'eventswp-calendar-js', 'eventswp_calendar', [
				'events' => rest_url( 'eventswp/v1/calendar-events' ),
			] );
		}
	}

	public function register_blocks() {
		wp_register_script(
			'eventswp-events-block',
			EVENTSWP_PLUGIN_URL . 'build/events-block/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
			EVENTSWP_VERSION,
			true
		);

		wp_localize_script(
			'eventswp-events-block',
			'eventswp_block_editor',
			[
				'pluginUrl' => trailingslashit( EVENTSWP_PLUGIN_URL ),
			]
		);

		wp_register_style(
			'eventswp-events-editor-style',
			EVENTSWP_PLUGIN_URL . 'build/events-block/index.css',
			[],
			EVENTSWP_VERSION
		);

		register_block_type(
			EVENTSWP_PLUGIN_DIR . 'build/events-block',
			[
				'render_callback' => 'eventswp_render_events_block',
			]
		);

		$events_block_render = EVENTSWP_PLUGIN_DIR . 'build/events-block/render.php';
		if ( file_exists( $events_block_render ) ) {
			include_once $events_block_render;
		}
	}

	public function maybe_override_calendar_page( $content ) {
		$calendar_page_id = get_option( 'eventswp_calendar_page_id' );
		if ( is_page( $calendar_page_id ) ) {
			ob_start();
			?>
			<div id="eventswp-calendar" class="my-10">
				<h2 class="text-2xl font-bold mb-4">Event Calendar</h2>
				<div class="max-w-6xl mx-auto p-4" id="eventswp-calendar"></div>
			</div>
			<?php
			return ob_get_clean();
		}
		return $content;
	}

	public function register_calendar_endpoint() {
		register_rest_route( 'eventswp/v1', '/calendar-events', [
			'methods'  => 'GET',
			'callback' => [ $this, 'get_calendar_events' ],
			'permission_callback' => '__return_true',
		] );
	}

	public function get_calendar_events( $request ) {
        $events = get_posts([
            'post_type'      => 'eventswp-event',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);
    
        $data = [];
    
        foreach ( $events as $event ) {
            $event_id = $event->ID;
    
            $date = get_post_meta( $event_id, 'event_date', true );
            $start = get_post_meta( $event_id, 'event_time', true );
            $end   = get_post_meta( $event_id, 'event_end_time', true );
    
            // Skip if date or start time is missing
            if ( ! $date || ! $start ) {
                continue;
            }
    
            // Ensure proper formatting
            $start_dt = strtotime( "$date $start" );
            $end_dt   = $end ? strtotime( "$date $end" ) : null;
    
            if ( ! $start_dt ) {
                continue;
            }
    
            $data[] = [
                'title'      => get_the_title( $event_id ),
                'start'      => date( 'c', $start_dt ),
                'end'        => $end_dt ? date( 'c', $end_dt ) : null,
                'url'        => get_permalink( $event_id ),
                'start_time' => date( 'g:i A', strtotime( $start ) ),
                'end_time'   => $end ? date( 'g:i A', strtotime( $end ) ) : null,
            ];
            
        }
    
        return rest_ensure_response( $data );
    }
    

	public function register_post_types() {
		register_post_type( 'eventswp-event', [
			'labels' => [
				'name' => __( 'Events', 'eventswp' ),
				'singular_name' => __( 'Event', 'eventswp' ),
				'menu_name' => __( 'Events', 'eventswp' ),
			],
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
			'menu_position' => 5,
			'menu_icon' => 'dashicons-calendar-alt',
		] );
	}

	public function register_taxonomies() {
		register_taxonomy( 'event-category', 'eventswp-event', [
			'label' => __( 'Event Categories', 'eventswp' ),
			'hierarchical' => true,
			'show_in_rest' => true,
			'public' => true,
		] );

		register_taxonomy( 'event-type', 'eventswp-event', [
			'label' => __( 'Event Types', 'eventswp' ),
			'hierarchical' => false,
			'show_in_rest' => true,
			'public' => true,
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
