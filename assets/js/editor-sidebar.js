(function (wp) {
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { TextControl, CheckboxControl, DatePicker } = wp.components;
	const { useSelect, useDispatch } = wp.data;
	const { createElement: el } = wp.element;

	const EventsMetaPanel = function () {
		const postType = useSelect((select) =>
			select('core/editor').getCurrentPostType()
		);

		if (postType !== 'eventswp-event') {
			return null; // ✅ Do not render the panel if not our post type
		}

		const meta = useSelect((select) =>
			select('core/editor').getEditedPostAttribute('meta') || {}
		, []);

		const { editPost } = useDispatch('core/editor');

		const get = (key) => (typeof meta[key] !== 'undefined' ? meta[key] : '');
		const set = (key, value) =>
			editPost({ meta: { ...meta, [key]: value } });

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'eventswp-event-details',
				title: 'Event Details',
				className: 'eventswp-event-sidebar',
			},
			el('p', {}, 'Event Date'),
			el(DatePicker, {
				currentDate: get('event_date'),
				onChange: (date) => set('event_date', date),
			}),
			el(TextControl, {
				label: 'Event Start Time',
				type: 'time',
				value: get('event_time'),
				onChange: (value) => set('event_time', value),
				inputProps: { step: 60 },
			}),
			el(TextControl, {
				label: 'Event End Time',
				type: 'time',
				value: get('event_end_time'),
				onChange: (value) => set('event_end_time', value),
				inputProps: { step: 60 },
			}),
			el(TextControl, {
				label: 'Venue Name',
				value: get('event_venue_name'),
				onChange: (value) => set('event_venue_name', value),
			}),
			el(TextControl, {
				label: 'Venue Address',
				value: get('event_venue_address'),
				onChange: (value) => set('event_venue_address', value),
			}),
			el(TextControl, {
				label: 'Contact Phone',
				value: get('event_contact_phone'),
				onChange: (value) => set('event_contact_phone', value),
			}),
			el(TextControl, {
				label: 'Contact Email',
				type: 'email',
				value: get('event_contact_email'),
				onChange: (value) => set('event_contact_email', value),
			}),
			el(CheckboxControl, {
				label: 'Show Google Map',
				checked: !!get('event_show_map'),
				onChange: (checked) => set('event_show_map', checked),
			})
		);
	};

	registerPlugin('eventswp-sidebar', {
		render: EventsMetaPanel,
		icon: 'calendar-alt',
	});
})(window.wp);
