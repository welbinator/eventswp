<?php
namespace EventsWP;

defined('ABSPATH') || exit;

add_action('wp_ajax_eventswp_filter_events', __NAMESPACE__ . '\\eventswp_handle_filter_events');
add_action('wp_ajax_nopriv_eventswp_filter_events', __NAMESPACE__ . '\\eventswp_handle_filter_events');

function eventswp_handle_filter_events() {
	$search     = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
	$categories = isset($_POST['categories']) ? json_decode(stripslashes($_POST['categories']), true) : [];
	$layout     = isset($_POST['layout']) ? sanitize_text_field($_POST['layout']) : 'grid';
	$columns    = isset($_POST['columns']) ? intval($_POST['columns']) : 3;

	$grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
	switch ($columns) {
		case 1: $grid_class = 'grid-cols-1'; break;
		case 2: $grid_class = 'grid-cols-1 sm:grid-cols-2'; break;
		case 3: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'; break;
		case 4: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'; break;
		case 5: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'; break;
		case 6: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6'; break;
	}

	$args = [
		'post_type'      => 'eventswp-event',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		's'              => $search,
	];

	if (!empty($categories)) {
		$args['tax_query'] = [[
			'taxonomy' => 'event-category',
			'field'    => 'term_id',
			'terms'    => $categories,
		]];
	}

	$query = new \WP_Query($args);

	ob_start();
	echo '<div class="' . esc_attr($layout === 'list' ? 'flex flex-col w-full' : 'grid ' . $grid_class . ' gap-6') . '">';

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			echo render_event_card($layout);
		}
	} else {
		echo '<p>No events found.</p>';
	}

	echo '</div>';
	wp_reset_postdata();

	wp_send_json_success([ 'html' => ob_get_clean() ]);
}
