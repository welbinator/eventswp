<?php
defined('ABSPATH') || exit;

if (!function_exists('eventswp_render_events_filters_block')) {
	function eventswp_render_events_filters_block($attributes, $content) {
		ob_start();

		$categories = get_terms([
			'taxonomy'   => 'event-category',
			'hide_empty' => false,
		]);

		$layout = isset($attributes['layout']) ? esc_attr($attributes['layout']) : 'grid';
		$columns = isset($attributes['columns']) ? intval($attributes['columns']) : 3;
		?>
		<form id="eventswp-filter-form" class="mb-6 space-y-4"
		      data-layout="<?php echo $layout; ?>"
		      data-columns="<?php echo $columns; ?>">

			<input type="text" name="event_search" placeholder="Search events..." class="w-full border px-4 py-2 rounded" />

			<div class="space-y-1">
				<?php foreach ( $categories as $cat ) : ?>
					<label class="block">
						<input type="checkbox" name="event_categories[]" value="<?php echo esc_attr($cat->term_id); ?>" />
						<?php echo esc_html($cat->name); ?>
					</label>
				<?php endforeach; ?>
			</div>

			<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
				Filter Events
			</button>
		</form>
		<?php

		return ob_get_clean();
	}
}
