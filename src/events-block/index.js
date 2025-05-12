import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';
import save from './save';

registerBlockType('eventswp/events-block', {
	edit,
	save
});
