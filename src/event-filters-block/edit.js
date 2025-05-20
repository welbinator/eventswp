import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	console.log("edit.js");
	return (
		
		<div {...useBlockProps()}>
			<p><strong>Event Filters:</strong> This block outputs a search box and category checkboxes on the frontend.</p>
		</div>
	);
}
