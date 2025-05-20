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
	$category       = !empty($categories) ? implode(', ', wp_list_pluck($categories, 'name')) : '';
	$type           = !empty($types) ? $types[0]->name : '';

	$date = $date_raw ? date_i18n('F j, Y', strtotime($date_raw)) : '';
	$time = $start ? date_i18n('g:i A', strtotime($start)) : '';
	$time .= ($end ? ' - ' . date_i18n('g:i A', strtotime($end)) : '');

	ob_start();
	?>
	<article class="<?= $layout === 'list' ? 'flex flex-col md:flex-row gap-6 !w-full py-6 border-b' : 'h-full flex flex-col rounded overflow-hidden'; ?>"
	         itemscope itemtype="https://schema.org/Event">

		<meta itemprop="url" content="<?= esc_url($link) ?>" />
		<meta itemprop="name" content="<?= esc_attr($title) ?>" />
		<?php if ($date_raw): ?>
			<meta itemprop="startDate" content="<?= esc_attr(date('c', strtotime("$date_raw $start"))) ?>" />
		<?php endif; ?>

		<?php if ($layout === 'list'): ?>
			<div class="relative md:w-[20%] aspect-[16/9]">
				<?php if ($image): ?>
					<img src="<?= esc_url($image) ?>"
					     alt="<?= esc_attr("Event image for $title") ?>"
					     class="inset-0 max-w-full max-h-full"
					     itemprop="image" />
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
						<span><?= esc_html($category) ?></span>
						<span><?= esc_html($type) ?></span>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="relative w-full aspect-[16/9] h-[200px]">
				<?php if ($image): ?>
					<img src="<?= esc_url($image) ?>"
					     alt="<?= esc_attr("Event image for $title") ?>"
					     class="inset-0 max-w-full max-h-full"
					     itemprop="image" />
				<?php endif; ?>
			</div>
			<div class="flex flex-col flex-grow p-4">
				<h3 class="text-lg font-semibold mb-2 line-clamp-2">
					<a href="<?= esc_url($link) ?>" class="hover:underline" itemprop="url">
						<span itemprop="name"><?= esc_html($title) ?></span>
					</a>
				</h3>
				<div class="flex flex-col gap-2 mt-auto">
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
					<div class="flex items-center justify-between text-sm mt-2">
						<span><?= esc_html($category) ?></span>
						<span><?= esc_html($type) ?></span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</article>
	<?php
	return ob_get_clean();
}
