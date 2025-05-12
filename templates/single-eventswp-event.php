<?php
defined( 'ABSPATH' ) || exit;

$event_id = get_the_ID();
$event_date = get_post_meta( $event_id, 'event_date', true );
$event_time = get_post_meta( $event_id, 'event_time', true );
$venue_name = get_post_meta( $event_id, 'event_venue_name', true );
$venue_address = get_post_meta( $event_id, 'event_venue_address', true );
$event_type = wp_get_post_terms( $event_id, 'event-type' );
$event_category = wp_get_post_terms( $event_id, 'event-category' );
$show_map = get_post_meta( $event_id, 'event_show_map', true );
$api_key = get_option( 'eventswp_google_maps_api_key' );
$featured_image_url = get_the_post_thumbnail_url( $event_id, 'full' );

get_header(); ?>

<div class="container">
	<article class="max-w-4xl mx-auto">
		<?php if ( $featured_image_url ) : ?>
			<div class="relative w-full aspect-[16/9] mb-6">
				<img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="object-cover rounded w-full h-full absolute inset-0" />
			</div>
		<?php endif; ?>

		<header class="mb-8">
			<h1 class="text-3xl font-bold mb-2"><?php the_title(); ?></h1>

			<?php if ( $event_date || $event_time || $venue_name ) : ?>
				<div class="flex flex-wrap gap-4 mb-4">
					<?php if ( $event_date || $event_time ) : ?>
						<div class="flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar w-5 h-5 mr-2"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
							<span><?php echo esc_html( date_i18n( 'F j, Y', strtotime( $event_date ) ) ); ?><?php if ( $event_time ) echo ' • ' . esc_html( $event_time ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $venue_name ) : ?>
						<div class="flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin w-5 h-5 mr-2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<span><?php echo esc_html( $venue_name ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $event_category ) || ! empty( $event_type ) ) : ?>
				<div class="flex flex-wrap gap-4">
					<?php if ( ! empty( $event_category ) ) : ?>
						<div class="flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag w-5 h-5 mr-2"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"></path><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle></svg>
							<span><?php echo esc_html( $event_category[0]->name ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $event_type ) ) : ?>
						<div class="flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users w-5 h-5 mr-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
							<span><?php echo esc_html( $event_type[0]->name ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</header>

		<section class="mb-8">
			<h2 class="text-xl font-semibold mb-4">About This Event</h2>
			<div class="prose max-w-none"><?php the_content(); ?></div>
		</section>

		<?php if ( $venue_address ) : ?>
			<section class="mb-8">
				<h2 class="text-xl font-semibold mb-4">Location</h2>
				<p class="mb-4"><?php echo esc_html( $venue_address ); ?></p>

				<?php if ( $show_map && $api_key ) : ?>
					<div class="w-full h-80 rounded overflow-hidden">
						<iframe
							src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr( $api_key ); ?>&q=<?php echo esc_attr( urlencode( $venue_address ) ); ?>"
							width="100%"
							height="100%"
							style="border:0"
							loading="lazy"
							allowfullscreen
							referrerpolicy="no-referrer-when-downgrade"
							title="Event Location Map"
						></iframe>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>
	</article>
    </div>

<?php get_footer(); ?>
