/* global wp */
import edit from './edit';
import save from './save';

const {
	i18n: {
		__
	},
	blocks: {
		registerBlockType,
	},
} = wp;

registerBlockType('board-game-collector/single-game', {
	title: __( 'Insert Board Game' ),
	description: __( 'Insert a reference to a board game in your collection.' ),
	category: 'widgets',
	icon: 'wordpress',
	keywords: [],
	attributes: {
		url: {
			type: 'string',
			source: 'attribute',
			attribute: 'href',
			selector: '.board-game-collector-single-game__link',
		},
		title: {
			type: 'string',
		},
		gameId: {
			type: 'string',
			source: 'attribute',
			attribute: 'data-id',
			selector: '.board-game-collector-single-game__details',
		}
	},
	example: {
		attributes: {
			search: 'Hello World',
		}
	},
	edit,
	save,
});
