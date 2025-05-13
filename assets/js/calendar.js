document.addEventListener('DOMContentLoaded', function () {
	const calendarEl = document.getElementById('eventswp-calendar');
	if (!calendarEl) return;

	const calendar = new FullCalendar.Calendar(calendarEl, {
		initialView: 'dayGridMonth',
		eventDisplay: 'block',
		events: function (fetchInfo, successCallback, failureCallback) {
            fetch(eventswp_calendar.events + `?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        
		eventContent: function (arg) {
            const start = arg.event.extendedProps.start_time || '';
            const end   = arg.event.extendedProps.end_time || '';
            const timeHTML = `<div class="eventswp-time">${start}${end ? ' - ' + end : ''}</div>`;
            const titleHTML = `<div class="eventswp-title"><a href="${arg.event.url}">${arg.event.title}</a></div>`;
        
            return {
                html: timeHTML + titleHTML
            };
        }
        
	});

	calendar.render();
});
