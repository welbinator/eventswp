<?php
namespace EventsWP;

defined('ABSPATH') || exit;

use EventsWP\Meta;
require_once EVENTSWP_PLUGIN_DIR . 'includes/register-meta.php';
use EventsWP\Settings;
require_once EVENTSWP_PLUGIN_DIR . 'includes/class-settings.php';
require_once EVENTSWP_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once EVENTSWP_PLUGIN_DIR . 'includes/template-functions.php';


class Plugin {

	public function init() {
		add_action('init', [ $this, 'register_post_types' ]);
		add_action('init', [ $this, 'register_taxonomies' ]);
		add_action('wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles' ]);
		add_action('wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ]);
		add_action('init', [ $this, 'register_blocks' ]);

		Meta::init();
		Settings::init();

		add_action('enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ]);
		add_filter('single_template', [ $this, 'load_custom_single_template' ]);
		add_filter('the_content', [ $this, 'maybe_override_calendar_page' ]);
		add_action('rest_api_init', [ $this, 'register_calendar_endpoint' ]);
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

		$calendar_page_id = get_option('eventswp_calendar_page_id');
		if (is_page($calendar_page_id)) {
			wp_enqueue_style(
				'fullcalendar-css',
				'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css',
				[],
				'6.1.11'
			);
			wp_enqueue_style(
				'eventswp-calendar-custom',
				EVENTSWP_PLUGIN_URL . 'assets/css/calendar-custom.css',
				[ 'fullcalendar-css' ],
				EVENTSWP_VERSION
			);
		}
	}

	public function enqueue_frontend_scripts() {
	$calendar_page_id = get_option('eventswp_calendar_page_id');

	if (is_page($calendar_page_id)) {
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
		wp_localize_script('eventswp-calendar-js', 'eventswp_calendar', [
			'events' => rest_url('eventswp/v1/calendar-events'),
		]);
	}

	// ✅ Localize for frontend filter block (index.js handles view logic too)
	if (has_block('eventswp/event-filters-block')) {
		wp_enqueue_script(
			'eventswp-event-filters-block',
			EVENTSWP_PLUGIN_URL . 'build/event-filters-block/index.js',
			[],
			EVENTSWP_VERSION,
			true
		);
		wp_localize_script('eventswp-event-filters-block', 'eventswp_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
		]);
	}
}


	public function register_blocks() {
		// Events block
		wp_register_script(
			'eventswp-events-block',
			EVENTSWP_PLUGIN_URL . 'build/events-block/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
			EVENTSWP_VERSION,
			true
		);
		wp_localize_script('eventswp-events-block', 'eventswp_block_editor', [
			'pluginUrl' => trailingslashit(EVENTSWP_PLUGIN_URL),
		]);
		wp_register_style(
			'eventswp-events-editor-style',
			EVENTSWP_PLUGIN_URL . 'build/events-block/index.css',
			[],
			EVENTSWP_VERSION
		);
		register_block_type(EVENTSWP_PLUGIN_DIR . 'build/events-block', [
			'render_callback' => 'eventswp_render_events_block',
		]);
		$events_block_render = EVENTSWP_PLUGIN_DIR . 'build/events-block/render.php';
		if (file_exists($events_block_render)) {
			include_once $events_block_render;
		}

		wp_register_script(
			'eventswp-event-filters-block',
			EVENTSWP_PLUGIN_URL . 'build/event-filters-block/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
			EVENTSWP_VERSION,
			true
		);
		wp_localize_script('eventswp-event-filters-block', 'eventswp_block_editor', [
			'pluginUrl' => trailingslashit(EVENTSWP_PLUGIN_URL),
		]);
		// Filters block
		register_block_type(EVENTSWP_PLUGIN_DIR . 'build/event-filters-block', [
			'render_callback' => 'eventswp_render_events_filters_block',
		]);
		$filters_block_render = EVENTSWP_PLUGIN_DIR . 'build/event-filters-block/render.php';
		if (file_exists($filters_block_render)) {
			include_once $filters_block_render;
		}
	}

	public function maybe_override_calendar_page($content) {
		$calendar_page_id = get_option('eventswp_calendar_page_id');
		if (is_page($calendar_page_id)) {
			ob_start();
			?>
			<div class="my-10">
				<?php
				$hide_title = get_option('eventswp_hide_calendar_title');
				$title = get_option('eventswp_calendar_title', 'Event Calendar');
				if (! $hide_title && ! empty($title)) {
					echo '<h2 class="text-2xl font-bold mb-4">' . esc_html($title) . '</h2>';
				}
				?>
				<div class="max-w-6xl mx-auto p-4">
					<div id="eventswp-calendar"></div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
		return $content;
	}

	public function register_calendar_endpoint() {
		register_rest_route('eventswp/v1', '/calendar-events', [
			'methods'  => 'GET',
			'callback' => [ $this, 'get_calendar_events' ],
			'permission_callback' => '__return_true',
		]);
	}

	public function get_calendar_events($request) {
		$start_param = $request->get_param('start');
		$end_param   = $request->get_param('end');

		$meta_query = [];
		if ($start_param && $end_param) {
			$meta_query[] = [
				'key'     => 'event_date',
				'value'   => [ $start_param, $end_param ],
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			];
		}

		$query = new \WP_Query([
			'post_type'      => 'eventswp-event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => $meta_query,
		]);

		$data = [];
		while ($query->have_posts()) {
			$query->the_post();
			$event_id = get_the_ID();
			$date  = get_post_meta($event_id, 'event_date', true);
			$start = get_post_meta($event_id, 'event_time', true);
			$end   = get_post_meta($event_id, 'event_end_time', true);

			if (! $date || ! $start) continue;

			$start_dt = strtotime("$date $start");
			$end_dt   = $end ? strtotime("$date $end") : null;

			$data[] = [
				'title'      => get_the_title(),
				'start'      => date('c', $start_dt),
				'end'        => $end_dt ? date('c', $end_dt) : null,
				'url'        => get_permalink(),
				'start_time' => date('g:i A', strtotime($start)),
				'end_time'   => $end ? date('g:i A', strtotime($end)) : null,
			];
		}
		wp_reset_postdata();
		return rest_ensure_response($data);
	}

	public function register_post_types() {
		register_post_type('eventswp-event', [
			'labels' => [
				'name' => __('Events', 'eventswp'),
				'singular_name' => __('Event', 'eventswp')
			],
			'public' => true,
			'has_archive' => true,
			'show_in_rest' => true,
			'supports' => [ 'title', 'editor', 'thumbnail' ],
			'menu_position' => 5,
			'menu_icon' => 'dashicons-calendar-alt',
		]);
	}

	public function register_taxonomies() {
		register_taxonomy('event-category', 'eventswp-event', [
			'label' => __('Event Categories', 'eventswp'),
			'hierarchical' => true,
			'show_in_rest' => true,
			'public' => true,
		]);

		register_taxonomy('event-type', 'eventswp-event', [
			'label' => __('Event Types', 'eventswp'),
			'hierarchical' => false,
			'show_in_rest' => true,
			'public' => true,
		]);
	}

	public function load_custom_single_template($template) {
		if (is_singular('eventswp-event')) {
			$custom = EVENTSWP_PLUGIN_DIR . 'templates/single-eventswp-event.php';
			if (file_exists($custom)) {
				return $custom;
			}
		}
		return $template;
	}
}
