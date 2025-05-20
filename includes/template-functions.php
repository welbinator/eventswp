<?php
namespace EventsWP;

defined('ABSPATH') || exit;

function render_event_card($layout = 'grid') {
	$event_id       = get_the_ID();
	$title          = get_the_title();
	$link           = get_permalink();
	$excerpt        = get_the_excerpt();
	$image          = get_the_post_thumbnail_url($event_id, 'large');
	$date_raw       = get_post_meta($event_id, 'event_date', true);
	$start          = get_post_meta($event_id, 'event_time', true);
	$end            = get_post_meta($event_id, 'event_end_time', true);
	$venue_name     = get_post_meta($event_id, 'event_venue_name', true);
	$venue_address  = get_post_meta($event_id, 'event_venue_address', true);
	$categories     = wp_get_post_terms($event_id, 'event-category');
	$types          = wp_get_post_terms($event_id, 'event-type');
	$category_names = wp_list_pluck($categories, 'name');
	$type           = !empty($types) ? $types[0]->name : '';

	$date = $date_raw ? date_i18n('l, F j, Y', strtotime($date_raw)) : '';
	$time = $start ? date_i18n('g:i A', strtotime($start)) : '';
	$time .= ($end ? ' - ' . date_i18n('g:i A', strtotime($end)) : '');

	ob_start();
	?>

	<?php if ($layout === 'list'): ?>
		<article class="flex flex-col md:flex-row gap-6 !w-full py-6 border-b" itemscope itemtype="https://schema.org/Event">
			<meta itemprop="url" content="<?= esc_url($link) ?>" />
			<meta itemprop="name" content="<?= esc_attr($title) ?>" />
			<?php if ($date_raw): ?>
				<meta itemprop="startDate" content="<?= esc_attr(date('c', strtotime("$date_raw $start"))) ?>" />
			<?php endif; ?>

			<div class="relative md:w-[20%] aspect-[16/9]">
				<?php if ($image): ?>
					<img src="<?= esc_url($image) ?>" alt="<?= esc_attr("Event image for $title") ?>" class="inset-0 max-w-full max-h-full" itemprop="image" />
				<?php endif; ?>
			</div>
			<div class="flex flex-col flex-grow md:w-[80%]">
				<h3 class="text-xl font-semibold mb-2">
					<a href="<?= esc_url($link) ?>" class="hover:underline" itemprop="url">
						<span itemprop="name"><?= esc_html($title) ?></span>
					</a>
				</h3>
				<p class="line-clamp-2" itemprop="description"><?= esc_html($excerpt) ?></p>
				<div class="flex flex-col gap-2">
					<?php if ($date): ?>
						<div class="flex items-center text-sm" itemprop="startDate" content="<?= esc_attr(date('c', strtotime($date_raw))) ?>">
							<?= esc_html("$date • $time") ?>
						</div>
					<?php endif; ?>
					<?php if ($venue_name || $venue_address): ?>
						<div class="flex items-center text-sm" itemprop="location" itemscope itemtype="https://schema.org/Place">
							<span itemprop="name"><?= esc_html($venue_name) ?></span>
							<?php if ($venue_address): ?>
								<meta itemprop="address" content="<?= esc_attr($venue_address) ?>" />
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="flex flex-wrap justify-between text-sm mt-2">
						<span><?= esc_html(implode(', ', $category_names)) ?></span>
						<span><?= esc_html($type) ?></span>
					</div>
				</div>
			</div>
		</article>
	<?php else: ?>
		<a href="<?= esc_url($link) ?>" class="rounded-lg border bg-card text-card-foreground shadow-sm overflow-hidden h-full transition-all hover:shadow-md block border-gray-200
" itemscope itemtype="https://schema.org/Event">
			<meta itemprop="url" content="<?= esc_url($link) ?>" />
			<meta itemprop="name" content="<?= esc_attr($title) ?>" />
			<?php if ($date_raw): ?>
				<meta itemprop="startDate" content="<?= esc_attr(date('c', strtotime("$date_raw $start"))) ?>" />
			<?php endif; ?>

			<div class="relative h-48 w-full">
				<?php if ($type): ?>
					<div class="absolute top-2 right-2 z-10">
						<div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none border-transparent bg-purple-500 text-white">
							<?= esc_html($type) ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($image): ?>
					<img src="<?= esc_url($image) ?>" alt="<?= esc_attr("Event image for $title") ?>" class="!h-full !w-full object-cover" itemprop="image" />
				<?php endif; ?>
			</div>

			<div class="p-4">
				<h3 class="font-semibold text-lg line-clamp-1"><?= esc_html($title) ?></h3>
				<div class="mt-3 space-y-2">
					<?php if ($date): ?>
						<div class="flex items-center text-sm text-gray-500">
							<svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-calendar mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M8 2v4M16 2v4M3 10h18M3 4h18a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
							<span><?= esc_html($date) ?></span>
						</div>
					<?php endif; ?>
					<?php if ($venue_name): ?>
						<div class="flex items-center text-sm text-gray-500">
							<svg xmlns="http://www.w3.org/2000/svg" class="lucide lucide-map-pin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
							<span class="truncate"><?= esc_html($venue_name) ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!empty($category_names)): ?>
				<div class="items-center p-4 pt-0 flex flex-wrap gap-1">
					<?php foreach ($category_names as $cat): ?>
						<div class="inline-flex items-center rounded-full border px-2.5 py-0.5 transition-colors focus:outline-none border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 font-normal text-xs">
							<?= esc_html($cat) ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<?php
	return ob_get_clean();
}
