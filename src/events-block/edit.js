import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<p><em>{__('Events will be displayed on the front end.', 'eventswp')}</em></p>
		</div>
	);
}
