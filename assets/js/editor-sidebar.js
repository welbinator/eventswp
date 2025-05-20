(function (wp) {
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { TextControl, CheckboxControl, DatePicker, SelectControl } = wp.components;
	const { useSelect, useDispatch } = wp.data;
	const { createElement: el } = wp.element;

	const EventsMetaPanel = function () {
		const postType = useSelect((select) =>
			select('core/editor').getCurrentPostType()
		);

		if (postType !== 'eventswp-event') {
			return null;
		}

		const postId = useSelect((select) =>
			select('core/editor').getCurrentPostId()
		);

		const meta = useSelect((select) =>
			select('core/editor').getEditedPostAttribute('meta') || {}
		, []);

		const selectedTerms = useSelect((select) =>
			select('core/editor').getEditedPostAttribute('event-type') || []
		, []);

		const eventTypeTerms = useSelect((select) =>
			select('core').getEntityRecords('taxonomy', 'event-type', { per_page: -1 })
		, []);

		const { editPost } = useDispatch('core/editor');
		const { editEntityRecord } = useDispatch('core');

		const get = (key) => (typeof meta[key] !== 'undefined' ? meta[key] : '');
		const set = (key, value) =>
			editPost({ meta: { ...meta, [key]: value } });

		const selectedEventType = selectedTerms.length ? selectedTerms[0] : '';

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
				onChange: function (date) {
					const formatted = new Date(date).toISOString().split('T')[0];
					set('event_date', formatted);
				}
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
			el(SelectControl, {
			label: 'Event Type',
			value: useSelect((select) => {
				const terms = select('core/editor').getEditedPostAttribute('event-type') || [];
				return terms.length > 0 ? terms[0] : '';
			}, []),
			options: useSelect((select) => {
				const items = select('core').getEntityRecords('taxonomy', 'event-type', { per_page: -1 });
				if (!items) return [{ label: 'Loading...', value: '' }];
				return [
					{ label: 'Select a type', value: '' },
					...items.map((term) => ({
						label: term.name,
						value: term.id.toString()
					}))
				];
			}, []),
			onChange: (termId) => {
				const { editPost } = wp.data.dispatch('core/editor');
				editPost({ 'event-type': termId ? [parseInt(termId)] : [] });
			}
		})

		);
	};

	registerPlugin('eventswp-sidebar', {
		render: EventsMetaPanel,
		icon: 'calendar-alt',
	});
})(window.wp);
