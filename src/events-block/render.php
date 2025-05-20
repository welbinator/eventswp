<?php
defined('ABSPATH') || exit;

if (!function_exists('eventswp_render_events_block')) {
	function eventswp_render_events_block($attributes, $content) {
		ob_start();

		$columns   = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
		$layout    = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
		$cat_ids   = isset($attributes['categories']) && is_array($attributes['categories']) ? array_filter($attributes['categories']) : [];

		$grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
		switch ($columns) {
			case 1: $grid_class = 'grid-cols-1'; break;
			case 2: $grid_class = 'grid-cols-1 sm:grid-cols-2'; break;
			case 3: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'; break;
			case 4: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'; break;
			case 5: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'; break;
			case 6: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6'; break;
		}

		$query_args = [
			'post_type'      => 'eventswp-event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		];

		if (!empty($cat_ids)) {
			$query_args['tax_query'] = [
				[
					'taxonomy' => 'event-category',
					'field'    => 'term_id',
					'terms'    => $cat_ids,
					'operator' => 'IN',
				],
			];
		}

		$events = new WP_Query($query_args);

		echo '<div id="eventswp-results" class="container mx-auto px-4 py-8">';

		if ($events->have_posts()) {
			echo $layout === 'list'
				? '<div class="flex flex-col w-full">'
				: '<div class="grid ' . esc_attr($grid_class) . ' gap-6">';

			while ($events->have_posts()) {
				$events->the_post();
				echo \EventsWP\render_event_card($layout); // ✅ Uses shared rendering
			}

			echo '</div>';
		} else {
			echo '<p>' . esc_html__('No events found.', 'eventswp') . '</p>';
		}

		echo '</div>';
		wp_reset_postdata();

		return ob_get_clean();
	}
}
