import './style.scss';
import './editor.scss';
import Edit from './edit';
import metadata from './block.json';

const { name } = metadata;

wp.blocks.registerBlockType(name, {
	...metadata,
	edit: Edit,
	save: () => null,
});
