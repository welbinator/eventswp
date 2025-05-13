import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, CheckboxControl, Spinner } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
	const { columns, layout, categories } = attributes;
	const [allCategories, setAllCategories] = useState([]);
	const [loading, setLoading] = useState(true);

	useEffect(() => {
		wp.apiFetch({ path: '/wp/v2/event-category?per_page=100' }).then((cats) => {
			setAllCategories(cats);
			setLoading(false);
		});
	}, []);

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

	const toggleCategory = (id) => {
		const newCats = categories.includes(id)
			? categories.filter((c) => c !== id)
			: [...categories, id];
		setAttributes({ categories: newCats });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'eventswp')} initialOpen={true}>
					<SelectControl
						label={__('Layout', 'eventswp')}
						value={layout}
						options={[
							{ label: 'Grid', value: 'grid' },
							{ label: 'List View', value: 'list' }
						]}
						onChange={(value) => setAttributes({ layout: value })}
					/>

					{layout === 'grid' && (
						<SelectControl
							label={__('Number of Columns', 'eventswp')}
							value={columns}
							options={[1, 2, 3, 4, 5, 6].map((num) => ({
								label: `${num}`,
								value: num,
							}))}
							onChange={(value) => setAttributes({ columns: parseInt(value, 10) })}
						/>
					)}

					{loading ? <Spinner /> : allCategories.map((cat) => (
						<CheckboxControl
							key={cat.id}
							label={cat.name}
							checked={categories.includes(cat.id)}
							onChange={() => toggleCategory(cat.id)}
						/>
					))}
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{mockEvents}
			</div>
		</>
	);
}
