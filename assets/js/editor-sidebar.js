(function (wp) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var TextControl = wp.components.TextControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var DatePicker = wp.components.DatePicker;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var el = wp.element.createElement;

	var EventsMetaPanel = function () {
		var meta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);

		var editPost = useDispatch('core/editor').editPost;

		var get = function (key) {
			return typeof meta[key] !== 'undefined' ? meta[key] : '';
		};

		var set = function (key, value) {
			var updated = {};
			updated[key] = value;
			editPost({ meta: Object.assign({}, meta, updated) });
		};

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'eventswp-event-details',
				title: 'Event Details',
				className: 'eventswp-event-sidebar'
			},
			el('p', {}, 'Event Date'),
			el(DatePicker, {
				currentDate: get('event_date'),
				onChange: function (date) {
					set('event_date', date);
				}
			}),
			el(TextControl, {
				label: 'Event Start Time',
				type: 'time',
				value: get('event_time'),
				onChange: function (value) {
					set('event_time', value);
				},
				inputProps: { step: 60 }
			}),
			
			el(TextControl, {
				label: 'Event End Time',
				type: 'time',
				value: get('event_end_time'),
				onChange: function (value) {
					set('event_end_time', value);
				},
				inputProps: { step: 60 }
			}),
            el(TextControl, {
                label: 'Venue Name',
                value: get('event_venue_name'),
                onChange: function (value) {
                    set('event_venue_name', value);
                }
            }),
            // Venue Address
            el(TextControl, {
                label: 'Venue Address',
                value: get('event_venue_address'),
                onChange: function (value) {
                    set('event_venue_address', value);
                }
            }),

			el(TextControl, {
				label: 'Contact Phone',
				value: get('event_contact_phone'),
				onChange: function (value) {
					set('event_contact_phone', value);
				}
			}),
			el(TextControl, {
				label: 'Contact Email',
				type: 'email',
				value: get('event_contact_email'),
				onChange: function (value) {
					set('event_contact_email', value);
				}
			}),
			el(CheckboxControl, {
				label: 'Show Google Map',
				checked: !!get('event_show_map'),
				onChange: function (checked) {
					set('event_show_map', checked);
				}
			})
		);
	};

	registerPlugin('eventswp-sidebar', {
		render: EventsMetaPanel,
		icon: 'calendar-alt'
	});
})(window.wp);
