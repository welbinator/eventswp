document.addEventListener('DOMContentLoaded', function () {
	const calendarEl = document.getElementById('eventswp-calendar');
	if (!calendarEl) return;

	// Determine initial view based on screen width
	function getInitialView() {
		return window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth';
	}

	let calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: getInitialView(),
		eventDisplay: 'block',

		// Define custom listMonth view
		views: {
			listMonth: {
				type: 'list',
				duration: { months: 1 },
				buttonText: 'List Month'
			}
		},

		events: function (fetchInfo, successCallback, failureCallback) {
			fetch(eventswp_calendar.events)
				.then(response => response.json())
				.then(data => successCallback(data))
				.catch(error => failureCallback(error));
		},

		eventContent: function (arg) {
			const start = arg.event.extendedProps.start_time || '';
			const end = arg.event.extendedProps.end_time || '';
			const timeHTML = `<div class="eventswp-time">${start}${end ? ' - ' + end : ''}</div>`;
			const titleHTML = `<div class="eventswp-title"><a href="${arg.event.url}">${arg.event.title}</a></div>`;
			return { html: timeHTML + titleHTML };
		},

		windowResize: function () {
			const newView = getInitialView();
			if (calendar.view.type !== newView) {
				calendar.changeView(newView);
			}
		}
	});

	calendar.render();
});
