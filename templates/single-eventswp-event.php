<?php
defined('ABSPATH') || exit;

$event_id = get_the_ID();
$event_date = get_post_meta($event_id, 'event_date', true);
$event_time = get_post_meta($event_id, 'event_time', true);
$venue_name = get_post_meta($event_id, 'event_venue_name', true);
$venue_address = get_post_meta($event_id, 'event_venue_address', true);
$event_type = wp_get_post_terms($event_id, 'event-type');
$event_category = wp_get_post_terms($event_id, 'event-category');
$show_map = get_post_meta($event_id, 'event_show_map', true);
$api_key = get_option('eventswp_google_maps_api_key');
$featured_image_url = get_the_post_thumbnail_url($event_id, 'full');
$start_datetime = $event_date ? date('c', strtotime("$event_date $event_time")) : '';
?>

<?php get_header(); ?>

<?php if ( class_exists( 'OCEANWP_Theme_Class' ) ) : ?>
<div id="content-wrap" class="container">
	<div id="primary" class="content-area">
		<div id="content" class="site-content">
<?php endif; ?>
			<article class="min-h-screen bg-white" itemscope itemtype="https://schema.org/Event">
	<meta itemprop="url" content="<?php the_permalink(); ?>">
	<meta itemprop="name" content="<?php the_title_attribute(); ?>">
	<?php if ($start_datetime): ?>
		<meta itemprop="startDate" content="<?php echo esc_attr($start_datetime); ?>">
	<?php endif; ?>

	<div class="relative h-[40vh] md:h-[40vh] lg:h-[40vh] w-full overflow-hidden">
		<div class="absolute inset-0 bg-black/40 z-10"></div>
		<?php if ($featured_image_url): ?>
			<img src="<?php echo esc_url($featured_image_url); ?>"
				 alt="<?php echo esc_attr(get_the_title() . ' featured image'); ?>"
				 class="!w-full !h-full object-cover"
				 itemprop="image">
		<?php endif; ?>
		<div class="absolute bottom-0 left-0 right-0 z-20 p-6 md:p-10 bg-gradient-to-t from-black/80 to-transparent">
			<div class="max-w-7xl mx-auto">
				<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold !text-white mb-2" itemprop="name"><?php the_title(); ?></h1>
				<div class="flex flex-wrap gap-2 mt-4">
					<?php if (!empty($event_type)) : ?>
						<div class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-purple-500 text-white">
							<?php echo esc_html($event_type[0]->name); ?>
						</div>
					<?php endif; ?>
					<?php if (!empty($event_category)) : ?>
						<?php foreach ($event_category as $cat) : ?>
							<div class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-white/10 backdrop-blur-sm text-white border border-white/20">
								<?php echo esc_html($cat->name); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="max-w-7xl mx-auto px-4 py-8 md:py-12">
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
			<div class="lg:col-span-2 space-y-8">
				<section itemprop="description">
					<h2 class="text-2xl font-bold mb-4">About This Event</h2>
					<div class="prose max-w-none"><?php the_content(); ?></div>
				</section>

				<div class="shrink-0 bg-border h-[1px] w-full"></div>

				<?php if ($venue_address || $venue_name): ?>
				<section>
					<h2 class="text-2xl font-bold mb-4">Location</h2>
					<div class="bg-gray-50 p-6 rounded-xl">
						<div class="flex items-start gap-3" itemprop="location" itemscope itemtype="https://schema.org/Place">
							<svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-map-pin h-5 w-5 text-gray-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<div>
								<?php if ($venue_name): ?>
									<h3 class="font-medium !m-0" itemprop="name"><?php echo esc_html($venue_name); ?></h3>
								<?php endif; ?>
								<?php if ($venue_address): ?>
									<p class="text-gray-600 mt-1" itemprop="address"><?php echo esc_html($venue_address); ?></p>
									<?php if ($show_map && $api_key): ?>
										<a href="https://maps.google.com/?q=<?php echo urlencode($venue_address); ?>" target="_blank" rel="noopener noreferrer" class="inline-block mt-3 text-sm font-medium text-primary hover:underline">View on map</a>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
				<?php endif; ?>
			</div>

			<div class="space-y-6">
				<div class="bg-gray-50 p-6 rounded-xl">
					<h2 class="text-xl font-bold mb-4">Event Details</h2>
					<div class="space-y-4">
						<?php if ($event_date): ?>
							<div class="flex gap-3">
								<svg class="lucide lucide-calendar h-5 w-5 text-gray-500" ...></svg>
								<div>
									<h3 class="!text-sm !font-medium !text-gray-500 !m-0">Date</h3>
									<p class="font-medium">
										<time datetime="<?php echo esc_attr($start_datetime); ?>" itemprop="startDate">
											<?php echo esc_html(date_i18n('l, F j, Y', strtotime($event_date))); ?>
										</time>
									</p>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($event_time): ?>
							<div class="flex gap-3">
								<svg class="lucide lucide-clock h-5 w-5 text-gray-500" ...></svg>
								<div>
									<h3 class="!text-sm !font-medium !text-gray-500 !m-0">Time</h3>
									<p class="font-medium"><?php echo esc_html($event_time); ?></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if (!empty($event_category)): ?>
							<div class="flex gap-3">
								<svg class="lucide lucide-tag h-5 w-5 text-gray-500" ...></svg>
								<div>
									<h3 class="!text-sm !font-medium !text-gray-500 !m-0">Categories</h3>
									<p class="font-medium"><?php echo esc_html(implode(', ', wp_list_pluck($event_category, 'name'))); ?></p>
								</div>
							</div>
						<?php endif; ?>
						<?php if (!empty($event_type)): ?>
							<div class="flex gap-3">
								<svg class="lucide lucide-users h-5 w-5 text-gray-500" ...></svg>
								<div>
									<h3 class="!text-sm !font-medium !text-gray-500 !m-0">Event Type</h3>
									<p class="font-medium"><?php echo esc_html($event_type[0]->name); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<a href="#register" class="inline-block mt-6 w-full text-center bg-primary text-white rounded-md px-8 py-3 text-sm font-medium hover:bg-primary/90">Register Now</a>
				</div>

				<div class="bg-primary/5 p-6 rounded-xl border border-primary/10 border-gray-200">
					<h3 class="font-medium mb-2">Share this event</h3>
					<div class="flex gap-3">
						<button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground !rounded-full !p-3 h-10 w-10">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class=""><path d="M16.99 0H20.298L13.071 8.26L21.573 19.5H14.916L9.702 12.683L3.736 19.5H0.426L8.156 10.665L0 0H6.826L11.539 6.231L16.99 0ZM15.829 17.52H17.662L5.83 1.876H3.863L15.829 17.52Z"></path></svg>
							<span class="sr-only">Share on X</span>
						</button>
						<button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground !rounded-full !p-3 h-10 w-10">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class=""><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
							<span class="sr-only">Share on Facebook</span>
						</button>
						<button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground !rounded-full !p-3 h-10 w-10">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class=""><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path></svg>
							<span class="sr-only">Share on LinkedIn</span>
						</button>
						<button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground !rounded-full !p-3 h-10 w-10">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class=""><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"></path></svg>
							<span class="sr-only">Share on Instagram</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>
<?php if ( class_exists( 'OCEANWP_Theme_Class' ) ) : ?>
		</div>
	</div>
</div>
<?php endif; ?>


<?php get_footer(); ?>
