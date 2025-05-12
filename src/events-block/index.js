import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';
import save from './save';
import './editor.scss';

registerBlockType('eventswp/events-block', {
	edit,
	save
});
