/* global wp, bgcollector */
import { render, createElement } from '@wordpress/element';

const {
	apiFetch
} = wp;

const updateCollection = (e) => {
	if (e) {
		e.preventDefault();
	}

	triggerUpdate();
};

const Button = () => (
	<button onClick={updateCollection}>Update Collection</button>
);

const triggerUpdate = () => {
	apiFetch.use( apiFetch.createNonceMiddleware( bgcollector.nonce ) );

	apiFetch(
		{
			path: 'bgc/v1/collection',
			method: 'POST',
			data: { username: document.getElementById( 'bgg-username' ).value }
		}
	).then( result => {
		const { games, status } = result;
		if ( 202 === status ) {
			setTimeout( function () {
				updateCollection();
			}, 5000 );
			return;
		}

		if ( 200 !== status ) {
			// @TODO Create admin notification for status notification.
			console.log( 'Something went wrong' );
		}

		if ( 0 !== games.length ) {
			triggerUpdate();
		}
	} ).catch( error => {
		// @TODO Create admin notification for error message.
		console.log( error );
	} );
};

const settings = () => {
	const username = document.getElementById('bgg-username');

	if (!username.value) {
		return;
	}

	render(
		createElement( Button ),
		document.getElementById( 'bgc-app' ) );
};

settings();

export default settings;
