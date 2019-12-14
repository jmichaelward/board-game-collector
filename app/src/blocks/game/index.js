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

registerBlockType('board-game-collector/game', {
	title: __( 'Insert Board Game' ),
	description: __( 'Insert a reference to a board game in your collection.' ),
	category: 'widgets',
	icon: 'wordpress',
	keywords: [],
	attributes: {
		url: {
			type: 'string',
			default: 'http://bgcollector.localhost/games/yokohama/',
		},
		thumbnail: {
			type: 'string',
			default: 'https://bgcollector.localhost/wp-content/uploads/2019/10/pic3600984-215x300.jpg'
		},
		title: {
			type: 'string',
			default: 'Yokohama',
		}
	},
	edit,
	save,
});
