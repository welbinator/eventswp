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

$start_datetime = $event_date ? date( 'c', strtotime( "$event_date $event_time" ) ) : '';
?>

<?php get_header(); ?>

<div class="container">
	<article class="max-w-4xl mx-auto" itemscope itemtype="https://schema.org/Event">
		<meta itemprop="url" content="<?php the_permalink(); ?>">
		<meta itemprop="name" content="<?php the_title_attribute(); ?>">
		<?php if ( $start_datetime ): ?>
			<meta itemprop="startDate" content="<?php echo esc_attr( $start_datetime ); ?>">
		<?php endif; ?>

		<?php if ( $featured_image_url ) : ?>
			<img src="<?php echo esc_url( $featured_image_url ); ?>"
			     alt="<?php echo esc_attr( get_the_title() . ' featured image' ); ?>"
			     class="object-cover rounded w-auto max-h-[300px] inset-0"
			     itemprop="image" />
		<?php endif; ?>

		<header class="mb-8">
			<h1 class="text-3xl font-bold mb-2" itemprop="name"><?php the_title(); ?></h1>

			<?php if ( $event_date || $event_time || $venue_name ) : ?>
				<div class="flex flex-wrap gap-4 mb-4">
					<?php if ( $event_date || $event_time ) : ?>
						<div class="flex items-center">
							<svg class="lucide lucide-calendar w-5 h-5 mr-2" ...></svg>
							<time itemprop="startDate" datetime="<?php echo esc_attr( $start_datetime ); ?>">
								<?php echo esc_html( date_i18n( 'F j, Y', strtotime( $event_date ) ) ); ?>
								<?php if ( $event_time ) echo ' • ' . esc_html( $event_time ); ?>
							</time>
						</div>
					<?php endif; ?>

					<?php if ( $venue_name ) : ?>
						<div class="flex items-center" itemprop="location" itemscope itemtype="https://schema.org/Place">
							<svg class="lucide lucide-map-pin w-5 h-5 mr-2" ...></svg>
							<span itemprop="name"><?php echo esc_html( $venue_name ); ?></span>
							<?php if ( $venue_address ): ?>
								<meta itemprop="address" content="<?php echo esc_attr( $venue_address ); ?>">
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $event_category ) || ! empty( $event_type ) ) : ?>
				<div class="flex flex-wrap gap-4">
					<?php if ( ! empty( $event_category ) ) : ?>
						<div class="flex items-center">
							<svg class="lucide lucide-tag w-5 h-5 mr-2" ...></svg>
							<span><?php echo esc_html( $event_category[0]->name ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $event_type ) ) : ?>
						<div class="flex items-center">
							<svg class="lucide lucide-users w-5 h-5 mr-2" ...></svg>
							<span><?php echo esc_html( $event_type[0]->name ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</header>

		<section class="mb-8" itemprop="description">
			<h2 class="text-xl font-semibold mb-4">About This Event</h2>
			<div class="prose max-w-none"><?php the_content(); ?></div>
		</section>

		<?php if ( $venue_address ) : ?>
			<section class="mb-8">
				<h2 class="text-xl font-semibold mb-4">Location</h2>
				<address class="not-italic mb-4"><?php echo esc_html( $venue_address ); ?></address>

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
