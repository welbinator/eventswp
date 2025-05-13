document.addEventListener('DOMContentLoaded', function () {
	const calendarEl = document.getElementById('eventswp-calendar');
	if (!calendarEl) return;

	const calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: 'dayGridMonth',
		events: function (fetchInfo, successCallback, failureCallback) {
			fetch(eventswp_calendar.events + '?per_page=100')
				.then(response => response.json())
				.then(data => {
					const events = data.map(post => {
						const meta = post.meta || {};
						return {
							title: post.title.rendered,
							start: meta.event_date || post.date,
							url: post.link,
						};
					});
					successCallback(events);
				})
				.catch(error => {
					console.error('Error fetching events:', error);
					failureCallback(error);
				});
		}
	});

	calendar.render();
});
