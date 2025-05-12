<?php
defined( 'ABSPATH' ) || exit;

function eventswp_render_events_block( $attributes, $content ) {
	ob_start();

	$events = new WP_Query([
		'post_type'      => 'eventswp-event',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	]);

	if ( $events->have_posts() ) {
		echo '<div class="container mx-auto px-4 py-8">';
		echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

		while ( $events->have_posts() ) {
			$events->the_post();

			$event_id = get_the_ID();
			$event_date = get_post_meta( $event_id, 'event_date', true );
			$event_time = get_post_meta( $event_id, 'event_time', true );
			$venue_name = get_post_meta( $event_id, 'event_venue_name', true );
			$venue_address = get_post_meta( $event_id, 'event_venue_address', true );
			$categories = wp_get_post_terms( $event_id, 'event-category' );
			$event_types = wp_get_post_terms( $event_id, 'event-type' );
			$category = ! empty( $categories ) ? $categories[0]->name : '';
			$event_type = ! empty( $event_types ) ? $event_types[0]->name : '';
			$image_url = get_the_post_thumbnail_url( $event_id, 'large' );
			$event_link = get_permalink( $event_id );

			echo '<div class="h-full"><article class="flex flex-col h-full rounded overflow-hidden">';
				echo '<div class="relative w-full aspect-[16/9]">';
					if ( $image_url ) {
						echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( get_the_title() ) . '" class="object-cover w-full h-full absolute inset-0" />';
					}
				echo '</div>';
				echo '<div class="flex flex-col flex-grow p-4">';
					echo '<h3 class="text-lg font-semibold mb-2 line-clamp-2">';
						echo '<a href="' . esc_url( $event_link ) . '" class="hover:underline">' . esc_html( get_the_title() ) . '</a>';
					echo '</h3>';
					echo '<div class="flex flex-col gap-2 mt-auto">';

						// Date
						if ( $event_date ) {
							echo '<div class="flex items-center text-sm">';
							echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-4 h-4 mr-2 flex-shrink-0"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>';
							echo '<span>' . esc_html( date_i18n( 'F j, Y', strtotime( $event_date ) ) ) . '</span>';
							echo '</div>';
						}

						// Venue
						if ( $venue_name || $venue_address ) {
							echo '<div class="flex items-center text-sm">';
							echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-4 h-4 mr-2 flex-shrink-0"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
							echo '<span class="truncate">' . esc_html( trim( $venue_name . ( $venue_address ? ', ' . $venue_address : '' ) ) ) . '</span>';
							echo '</div>';
						}

						// Category + Type
						echo '<div class="flex items-center justify-between text-sm mt-2">';
							echo '<span class="inline-flex items-center">';
							echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag w-4 h-4 mr-1 flex-shrink-0"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"></path><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle></svg>';
							echo esc_html( $category );
							echo '</span>';
							echo '<span class="text-sm">' . esc_html( $event_type ) . '</span>';
						echo '</div>';

					echo '</div>';
				echo '</div>';
			echo '</article></div>';
		}

		echo '</div></div>';
		wp_reset_postdata();
	} else {
		echo '<p>' . esc_html__( 'No events found.', 'eventswp' ) . '</p>';
	}

	return ob_get_clean();
}
