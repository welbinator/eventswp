import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const { columns } = attributes;

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
			<div {...useBlockProps()}>
				<p><em>{__('Events will be displayed on the front end.', 'eventswp')}</em></p>
			</div>
		</>
	);
}
