document.addEventListener('DOMContentLoaded', function () {
	const form = document.getElementById('eventswp-filter-form');
	if (!form) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();

		const formData = new FormData(form);
		const query = formData.get('event_search') || '';
		const categories = Array.from(formData.getAll('event_categories[]'));

		const data = new URLSearchParams();
		data.append('action', 'eventswp_filter_events');
		data.append('search', query);
		data.append('categories', JSON.stringify(categories));
		data.append('layout', form.dataset.layout || 'grid');
		data.append('columns', form.dataset.columns || 3);

		const resultsContainer = document.getElementById('eventswp-results');
		resultsContainer.innerHTML = '<p>Loading...</p>';

		fetch(eventswp_ajax.ajax_url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: data.toString()
		})
			.then(response => response.json())
			.then(json => {
				resultsContainer.innerHTML = json.success ? json.data.html : '<p>Error loading events.</p>';
			})
			.catch(() => {
				resultsContainer.innerHTML = '<p>Error loading events.</p>';
			});
	});
});
