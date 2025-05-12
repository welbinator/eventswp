import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const { columns } = attributes;

	const blockProps = useBlockProps({ className: 'eventswp-preview-grid' });

	const placeholder = '/wp-content/plugins/eventswp/assets/editor/img/placeholder.svg';


	const mockEvents = Array.from({ length: 4 }).map((_, i) => (
		<div className="eventswp-preview-item" key={i}>
			<div className="eventswp-preview-image">
				<img src={placeholder} alt="Placeholder" />
			</div>
			<h4 className="eventswp-preview-title">Event Title</h4>
			<p className="eventswp-preview-date">Jan 1, 2056</p>
			<p className="eventswp-preview-location">1234 Somewhere St, New York, NY 54321</p>
			<p className="eventswp-preview-category">Business</p>
		</div>
	));

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'eventswp')} initialOpen={true}>
					<SelectControl
						label={__('Number of Columns', 'eventswp')}
						value={columns}
						options={[1, 2, 3, 4, 5, 6].map((num) => ({
							label: `${num}`,
							value: num,
						}))}
						onChange={(value) => setAttributes({ columns: parseInt(value, 10) })}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{mockEvents}
			</div>
		</>
	);
}
