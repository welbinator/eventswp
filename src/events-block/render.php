<?php
defined('ABSPATH') || exit;

if (!function_exists('eventswp_render_events_block')) {
	function eventswp_render_events_block($attributes, $content)
	{
		ob_start();

		$columns = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
		$layout  = isset($attributes['layout']) ? $attributes['layout'] : 'grid';

		$grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
		switch ($columns) {
			case 1: $grid_class = 'grid-cols-1'; break;
			case 2: $grid_class = 'grid-cols-1 sm:grid-cols-2'; break;
			case 3: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'; break;
			case 4: $grid_class = 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'; break;
			case 5: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'; break;
			case 6: $grid_class = 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6'; break;
		}

		$events = new WP_Query([
			'post_type' => 'eventswp-event',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		]);

		if ($events->have_posts()) {
			echo '<div class="container mx-auto px-4 py-8">';
			echo $layout === 'list'
				? '<div class="flex flex-col w-full">'
				: '<div class="grid ' . esc_attr($grid_class) . ' gap-6">';

			while ($events->have_posts()) {
				$events->the_post();

				global $post;
				$event_id = $post->ID;
				$title = get_the_title();
				$link = get_permalink();
				$excerpt = get_the_excerpt();
				$image = get_the_post_thumbnail_url($event_id, 'large');
				$date_raw = get_post_meta($event_id, 'event_date', true);
				$start = get_post_meta($event_id, 'event_time', true);
				$end = get_post_meta($event_id, 'event_end_time', true);
				$venue_name = get_post_meta($event_id, 'event_venue_name', true);
				$venue_address = get_post_meta($event_id, 'event_venue_address', true);
				$categories = wp_get_post_terms($event_id, 'event-category');
				$types = wp_get_post_terms($event_id, 'event-type');
				$category = !empty($categories) ? $categories[0]->name : '';
				$type = !empty($types) ? $types[0]->name : '';

				$date = $date_raw ? date_i18n('F j, Y', strtotime($date_raw)) : '';
				$time = $start ? date_i18n('g:i A', strtotime($start)) : '';
				$time .= ($end ? ' - ' . date_i18n('g:i A', strtotime($end)) : '');

				if ($layout === 'list') {
					// List View layout
					echo '<article class="flex flex-col md:flex-row gap-6 w-full py-6 border-b">';
						echo '<div class="relative w-full md:w-1/3 aspect-[16/9]">';
							if ($image) {
								echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '" class="inset-0 max-w-full max-h-full" />';
							}
						echo '</div>';
						echo '<div class="flex flex-col flex-grow">';
							echo '<h3 class="text-xl font-semibold mb-2"><a href="' . esc_url($link) . '" class="hover:underline">' . esc_html($title) . '</a></h3>';
							echo '<p class="line-clamp-2">' . esc_html($excerpt) . '</p>';
							echo '<div class="flex flex-col gap-2">';

							if ($date) {
								echo '<div class="flex items-center text-sm">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 mr-2 flex-shrink-0"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>';
								echo '<span>' . esc_html("$date • $time") . '</span>';
								echo '</div>';
							}

							if ($venue_name || $venue_address) {
								echo '<div class="flex items-center text-sm">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-4 h-4 mr-2 flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
								echo '<span>' . esc_html(trim($venue_name . ($venue_address ? ', ' . $venue_address : ''))) . '</span>';
								echo '</div>';
							}

							echo '<div class="flex flex-wrap items-center justify-between text-sm mt-2">';
								echo '<span class="inline-flex items-center">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag w-4 h-4 mr-1 flex-shrink-0"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"></path><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle></svg>';
								echo esc_html($category);
								echo '</span>';
								echo '<span class="text-sm">' . esc_html($type) . '</span>';
							echo '</div>';

							echo '</div>';
						echo '</div>';
					echo '</article>';
				} else {
					// Grid layout
					echo '<div class="h-full"><article class="flex flex-col h-full rounded overflow-hidden">';
						echo '<div class="relative w-full aspect-[16/9] h-[200px]">';
						if ($image) {
							echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '" class="inset-0 max-w-full max-h-full" />';
						}
						echo '</div>';
						echo '<div class="flex flex-col flex-grow p-4">';
							echo '<h3 class="text-lg font-semibold mb-2 line-clamp-2">';
								echo '<a href="' . esc_url($link) . '" class="hover:underline">' . esc_html($title) . '</a>';
							echo '</h3>';
							echo '<div class="flex flex-col gap-2 mt-auto">';
							if ($date) {
								echo '<div class="flex items-center text-sm">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 mr-2 flex-shrink-0"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>';
								echo '<span>' . esc_html("$date • $time") . '</span>';
								echo '</div>';
							}
							if ($venue_name || $venue_address) {
								echo '<div class="flex items-center text-sm">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-4 h-4 mr-2 flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
								echo '<span>' . esc_html(trim($venue_name . ($venue_address ? ', ' . $venue_address : ''))) . '</span>';
								echo '</div>';
							}
							echo '<div class="flex items-center justify-between text-sm mt-2">';
								echo '<span class="inline-flex items-center">';
								echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag w-4 h-4 mr-1 flex-shrink-0"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"></path><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle></svg>';
								echo esc_html($category);
								echo '</span>';
								echo '<span class="text-sm">' . esc_html($type) . '</span>';
							echo '</div>';
						echo '</div></div>';
					echo '</article></div>';
				}
			}

			echo '</div></div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__('No events found.', 'eventswp') . '</p>';
		}

		return ob_get_clean();
	}
}
